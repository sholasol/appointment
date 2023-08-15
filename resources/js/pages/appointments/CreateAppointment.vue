<script setup>
import axios from "axios";
import { onMounted, reactive } from "vue";
import { useToastr } from "../../toaster.js";
import { useRouter } from "vue-router";
import { Form } from "vee-validate";
import flatpickr from "flatpickr";
import "flatpickr/dist/themes/light.css";

const toastr = useToastr();
const router = useRouter();

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
                // console.error("Request failed:", error);
            }
        });
};

onMounted(() => {
    flatpickr(".flatpickr", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        defaultHour: 10,
    });
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
                        <li class="breadcrumb-item active">Create</li>
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
                                                <option value="1">
                                                    Client One
                                                </option>
                                                <option value="2">
                                                    Client Two
                                                </option>
                                            </select>
                                        </div>
                                        <span class="invalid-feedback">{{
                                            errors.client
                                        }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">Start Time</label>
                                            <input
                                                :class="{
                                                    'is-invalid': errors.start,
                                                }"
                                                v-model="form.start"
                                                type="text"
                                                class="form-control flatpickr"
                                                id="start"
                                            />
                                        </div>
                                        <span class="invalid-feedback">{{
                                            errors.start
                                        }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="time">End Time</label>
                                            <input
                                                :class="{
                                                    'is-invalid': errors.end,
                                                }"
                                                v-model="form.end"
                                                type="text"
                                                class="form-control flatpickr"
                                                id="end"
                                            />
                                        </div>
                                        <span class="invalid-feedback">{{
                                            errors.end
                                        }}</span>
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
                                    Submit
                                </button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
