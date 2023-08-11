<script setup>
import { ref } from "vue";
import { useToastr } from "../../toaster.js";
import { Form, Field } from "vee-validate";

defineProps({
    user: Object,
    index: Number,
});

const editing = ref(false);

const toastr = useToastr();

const emit = defineEmits(["userDeleted"]);

const userIdBeingDeleted = ref(null);

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
            emit("userDeleted", userIdBeingDeleted.value);
        })
        .catch((error) => {
            // Handle error here if needed
            toastr.error("Oops! Something went wrong!");
            console.error(error);
        });
};
</script>

<template>
    <tr>
        <td>{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.created_at }}</td>
        <td>
            {{ user.role === 1 ? "Admin" : "User" }}
        </td>
        <td>
            <a
                @click.prevent="editUser(user)"
                class="fa fa-edit text-success"
            ></a>
            <a
                @click.prevent="confirmUserDeletion(user)"
                class="fa fa-trash text-danger ml-2"
            ></a>
        </td>
    </tr>

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
</template>
