<script setup>
import axios from "axios";
import { ref, onMounted, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toaster.js";
import { debounce } from "lodash";
import { Bootstrap4Pagination } from "laravel-vue-pagination";

const toastr = useToastr();

const users = ref([]);

const roles = ref([
    {
        name: "ADMIN",
        value: 1,
    },
    {
        name: "USER",
        value: 2,
    },
]);

const editing = ref(false);

const formValues = ref({});

const form = ref(null);

const userIdBeingDeleted = ref(null);

const getUsers = () => {
    axios
        .get("/api/users")
        .then((response) => {
            users.value = response.data;
        })
        .catch((error) => {
            console.error("Error fetching users:", error);
        });
};

//form validation
const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().required(),
    password: yup.string().required().min(8),
});

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().required(),
    // password: yup.string().when((password, schema) => {
    //     return password ? schema.required().min(8) : schema;
    // }),
});

const createUser = (values, { resetForm }) => {
    axios
        .post("/api/users", values)
        .then((response) => {
            users.value.unshift(response.data); //push/unshift add created user to existing
            $("#UserFormModal").modal("hide");
            toastr.success("User created successfully!");
            resetForm();
        })
        .catch((error) => {
            if (error.response.data.errors) {
                const errorMessages = Object.values(
                    error.response.data.errors
                ).flat();
                const errorMessage = errorMessages.join("<br>");
                toastr.error(errorMessage, "Error");
                setErrors(error.response.data.errors);

                //setErrors(error.response.data.errors);
            }
        });
};

const addUser = () => {
    editing.value = false;
    $("#UserFormModal").modal("show");
};

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $("#UserFormModal").modal("show");
    // formValues.value = user;
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const updateUser = (values, { setErrors }) => {
    axios
        .put("/api/users/" + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(
                (user) => user.id === response.data.id
            );
            users.value[index] = response.data;
            $("#UserFormModal").modal("hide");
            toastr.success("User updated successfully!");
        })
        .catch((error) => {
            const errorMessages = Object.values(
                error.response.data.errors
            ).flat();
            const errorMessage = errorMessages.join("<br>");
            toastr.error(errorMessage, "Error");
            setErrors(error.response.data.errors);

            // setErrors(error.response.data.errors);
        });
};

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
};

const confirmUserDeletion = (user) => {
    userIdBeingDeleted.value = user.id;
    $("#deleteUserModal").modal("show");
};

const deleteUser = () => {
    axios
        .delete(`/api/users/${userIdBeingDeleted.value}`)
        .then(() => {
            $("#deleteUserModal").modal("hide");
            toastr.success("User deleted successfully!");
            users.value = users.value.filter(
                (user) => user.id !== userIdBeingDeleted.value
            );
        })
        .catch((error) => {
            // Handle error here if needed
            toastr.error("Oops! Something went wrong!");
            console.error(error);
        });
};

const changeRole = (user, role) => {
    axios
        .patch(`/api/users/${user.id}/change-role`, {
            role: role,
        })
        .then(() => {
            toastr.success("Role changed successfully!");
        })
        .catch((error) => {
            // Handle error here if needed
            toastr.error("Oops! Something went wrong!");
            console.error(error);
        });
};

const searchQuery = ref(null);

const search = () => {
    axios
        .get(`/api/users/search?query=${searchQuery.value}`)
        .then((response) => {
            users.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

//autosearch table
watch(
    searchQuery,
    debounce(() => {
        search();
    }, 300)
);

const selectedUsers = ref([]);

const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);

    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log(selectedUsers.value);
};
const bulkDelete = () => {
    if (selectedUsers.value.length === 0) {
        alert("No users selected for deletion.");
        return;
    }
    axios
        .delete("/api/users", {
            data: {
                ids: selectedUsers.value,
            },
        })
        .then((response) => {
            users.value = users.value.filter(
                (user) => !selectedUsers.value.includes(user.id)
            );
            toastr.success(response.data.message);
            selectedUsers.value = [];
            selectAll.value = false;
        })
        .catch((error) => {
            console.error("Error deleting users:", error);
            toastr.error("An error occurred while deleting users.");
        });
};

const selectAll = ref(false);

const selectAllUsers = () => {
    //selectAll.value = !selectAll.value;

    if (selectAll.value) {
        selectedUsers.value = users.value.map((user) => user.id);
    } else {
        selectedUsers.value = [];
    }
};

onMounted(() => {
    getUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">List Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <button
                                @click="addUser"
                                type="button"
                                class="mb-2 btn btn-primary"
                            >
                                <i class="fa fa-plus-circle mr-1"></i>
                                Add New User
                            </button>

                            <button
                                v-if="selectedUsers.length > 0"
                                @click="bulkDelete"
                                type="button"
                                class="mb-2 btn btn-danger ml-2"
                            >
                                <i class="fa fa-trash"></i>
                                Delete Selected
                            </button>
                            <span
                                v-if="selectedUsers.length > 0"
                                class="text-success ml-2"
                                ><small
                                    >Selected {{ selectedUsers.length }} users
                                </small></span
                            >
                        </div>
                        <div class="d-flex">
                            <input
                                v-model="searchQuery"
                                type="text"
                                class="form-control"
                                placeholder="search"
                            />
                        </div>
                    </div>
                    <table class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>
                                    <input
                                        type="checkbox"
                                        v-model="selectAll"
                                        @change="selectAllUsers"
                                    />
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th width="5%">Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.length > 0">
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td>
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedUsers.includes(user.id)
                                        "
                                        @change="toggleSelection(user)"
                                    />
                                </td>
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.created_at }}</td>
                                <td>
                                    <!-- {{ user.role === 1 ? "Admin" : "User" }} -->
                                    <select
                                        class="form-control"
                                        @change="
                                            changeRole(
                                                user,
                                                $event.target.value
                                            )
                                        "
                                    >
                                        <option
                                            v-for="role in roles"
                                            :value="role.value"
                                            :key="role.value"
                                            :selected="user.role === role.value"
                                        >
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <a
                                        @click.prevent="editUser(user)"
                                        class="fa fa-edit text-success"
                                    ></a>
                                    <a
                                        @click.prevent="
                                            confirmUserDeletion(user)
                                        "
                                        class="fa fa-trash text-danger ml-2"
                                    ></a>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6">
                                    <p class="text-center">
                                        Oops! No record found to dispaly...
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create user Modal -->

    <div
        class="modal fade"
        id="UserFormModal"
        data-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Create User</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <Form
                    ref="form"
                    @submit="createUser"
                    :validation-schema="createUserSchema"
                    v-slot="{ errors }"
                > -->
                <Form
                    ref="form"
                    @submit="handleSubmit"
                    :validation-schema="
                        editing ? editUserSchema : createUserSchema
                    "
                    v-slot="{ errors }"
                    :initial-values="formValues"
                >
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label for="name" class="col-sm-2 col-form-label"
                                >Name</label
                            >
                            <div class="col-sm-10">
                                <Field
                                    name="name"
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    :class="{ 'is-invalid': errors.name }"
                                />
                                <span class="invalid-feedback">{{
                                    errors.name
                                }}</span>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="email" class="col-sm-2 col-form-label"
                                >Email</label
                            >
                            <div class="col-sm-10">
                                <Field
                                    type="text"
                                    name="email"
                                    class="form-control"
                                    id="email"
                                    :class="{ 'is-invalid': errors.email }"
                                />
                                <span class="invalid-feedback">{{
                                    errors.email
                                }}</span>
                            </div>
                        </div>
                        <div class="mb-3 row" v-if="editing"></div>
                        <div class="mb-3 row" v-else>
                            <label
                                for="password"
                                class="col-sm-2 col-form-label"
                                >Password</label
                            >
                            <div class="col-sm-10">
                                <Field
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    id="password"
                                    :class="{ 'is-invalid': errors.password }"
                                />
                                <span class="invalid-feedback">{{
                                    errors.password
                                }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span v-if="editing">Update User</span>
                            <span v-else>Create User</span>
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <!-- Delete dialog -->

    <div
        class="modal fade"
        id="deleteUserModal"
        data-backdrop="static"
        tabindex="-1"
        role="dialog"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Are you sure you want to delete this user?</h5>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>
                    <button
                        @click.prevent="deleteUser"
                        type="button"
                        class="btn btn-danger"
                    >
                        <span>Delete User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
