<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import { useToastr } from "../../toaster.js";
import { useRouter, useRoute } from "vue-router";
import { Form, Field } from "vee-validate";
import flatpickr from "flatpickr";
import "flatpickr/dist/themes/light.css";

const toastr = useToastr();
const router = useRouter();
const route = useRoute();

const form = reactive({
    title: "",
    client: "",
    start: "",
    end: "",
    description: "", // Fixed typo here
});

const handleSubmit = (values, actions) => {
    axios
        .post("/api/appointments", form)
        .then((response) => {
            console.log(values);
            toastr.success("Appointment created successfully!");
            router.push("/admin/appointment");
        })
        .catch((error) => {
            if (
                error.response &&
                error.response.data &&
                error.response.data.errors
            ) {
                const errorMessages = Object.values(
                    error.response.data.errors
                ).flat();
                const errorMessage = errorMessages.join("<br>");
                toastr.error(errorMessage, "Error");
            } else {
                console.error("Request failed:", error);
            }
        });
};

const clients = ref();
const getClients = () => {
    axios.get("/api/client").then((response) => {
        clients.value = response.data;
    });
};

const getAppointments = () => {
    axios.get(`/api/appointments/${route.params.id}/edit`).then(({ data }) => {
        form.title = data.title;
        form.client = data.client_id;
        form.start = data.start_time;
        form.end = data.end_time;
        form.description = data.description;
    });
};

const editMode = ref(false);

onMounted(() => {
    if (route.name === "admin.appointment.edit") {
        editMode.value = true;

        getAppointments();
    }

    flatpickr(".flatpickr", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        defaultHour: 10,
    });
    getClients();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard"
                                >Home</router-link
                            >
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/appointment"
                                >Appointments</router-link
                            >
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <span v-if="editMode"
                                ><h4>Edit Appointment</h4></span
                            >
                            <span v-else><h4>Create Appointment</h4></span>
                        </div>
                        <div class="card-body">
                            <Form
                                @submit="handleSubmit"
                                v-slot:default="{ errors }"
                            >
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input
                                                :class="{
                                                    'is-invalid': errors.title,
                                                }"
                                                v-model="form.title"
                                                type="text"
                                                class="form-control"
                                                id="title"
                                                placeholder="Enter Title"
                                            />
                                            <span class="invalid-feedback">{{
                                                errors.title
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client"
                                                >Client Name</label
                                            >
                                            <select
                                                :class="{
                                                    'is-invalid': errors.client,
                                                }"
                                                v-model="form.client"
                                                id="client"
                                                class="form-control"
                                            >
                                                <option
                                                    v-for="client in clients"
                                                    :key="client.id"
                                                    :value="client.id"
                                                >
                                                    {{ client.firstname }}
                                                    {{ client.lastname }}
                                                </option>
                                            </select>
                                            <span class="invalid-feedback">{{
                                                errors.client
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start"
                                                >Start Time</label
                                            >
                                            <input
                                                :class="{
                                                    'is-invalid': errors.start,
                                                }"
                                                v-model="form.start"
                                                type="text"
                                                class="form-control flatpickr"
                                                id="start"
                                            />
                                            <span class="invalid-feedback">{{
                                                errors.start
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end">End Time</label>
                                            <input
                                                :class="{
                                                    'is-invalid': errors.end,
                                                }"
                                                v-model="form.end"
                                                type="text"
                                                class="form-control flatpickr"
                                                id="end"
                                            />
                                            <span class="invalid-feedback">{{
                                                errors.end
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea
                                        :class="{
                                            'is-invalid': errors.description,
                                        }"
                                        v-model="form.description"
                                        class="form-control"
                                        id="description"
                                        rows="3"
                                        placeholder="Enter Description"
                                    ></textarea>
                                    <span class="invalid-feedback">{{
                                        errors.description
                                    }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <span v-if="editMode"
                                        >Update Appointment</span
                                    >
                                    <span v-else>Create Appointment</span>
                                </button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
