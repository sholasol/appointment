<script setup>
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";

const users = ref([]);

const editing = ref(false);

const formValues = ref({});

const form = ref(null);

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
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(8) : schema;
    }),
});

const createUser = (values, { resetForm }) => {
    axios
        .post("/api/users", values)
        .then((response) => {
            users.value.unshift(response.data); //push/unshift add created user to existing
            $("#UserFormModal").modal("hide");
            resetForm();
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
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
            //toastr.success('User updated successfully!');
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        });
};

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
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
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <button
                            @click="addUser"
                            type="button"
                            class="mb-2 btn btn-primary"
                        >
                            <i class="fa fa-plus-circle mr-1"></i>
                            Add New User
                        </button>
                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registered Date</th>
                                    <th>Role</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(user, index) in users"
                                    :key="user.id"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <a
                                            @click.prevent="editUser(user)"
                                            class="fa fa-edit text-success"
                                        ></a>
                                        <a
                                            data-toggle="modal"
                                            data-target="#deleteUserModal"
                                            class="fa fa-trash text-danger"
                                        ></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                            <label
                                for="staticEmail"
                                class="col-sm-2 col-form-label"
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
                            <label
                                for="staticEmail"
                                class="col-sm-2 col-form-label"
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
                        <div class="mb-3 row">
                            <label
                                for="inputPassword"
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

    <!-- Modal Form -->

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
                        <span>Create User</span>
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
                <Form
                    @submit="createUser"
                    :validation-schema="
                        editing ? editUserSchema : createUserSchema
                    "
                    v-slot="{ errors }"
                >
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label
                                for="staticEmail"
                                class="col-sm-2 col-form-label"
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
                            <label
                                for="staticEmail"
                                class="col-sm-2 col-form-label"
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
                        <div class="mb-3 row">
                            <label
                                for="inputPassword"
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
                            Save Changes
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>
