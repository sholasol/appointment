<script setup>
import axios from "axios";
import { ref, onMounted, watch } from "vue";
import Swal from "sweetalert2";
import { Bootstrap4Pagination } from "laravel-vue-pagination";

const appointments = ref([]);

const getBadgeClass = (status) => {
    return {
        badge: true,
        "badge-primary": status === 1,
        "badge-success": status === 2,
        "badge-danger": status === 3,
    };
};

const getAppointmentStatusText = (status) => {
    if (status === 1) {
        return "Scheduled";
    } else if (status === 2) {
        return "Confirmed";
    } else if (status === 3) {
        return "Cancelled";
    }
};

const appointmentStatus = { scheduled: 1, confirmed: 2, cancelled: 3 };

const getAppointments = (status) => {
    const params = {};

    if (status) {
        params.status = status;
    }

    return axios
        .get("/api/appointments", { params: params })
        .then((response) => {
            appointments.value = response.data;
        })
        .catch((error) => {
            console.error("Error fetching appointment:", error);
        });
};

// const deleteAppointment = () => {
//     axios
//         .delete(`/api/appointments/${appointmentIdBeingDeleted.value}`)
//         .then(() => {
//             $("#deleteAppointmentModal").modal("hide");
//             toastr.success("Appointment deleted successfully!");
//             appointments.value = appointments.value.filter(
//                 (appointment) =>
//                     appointment.id !== appointmentIdBeingDeleted.value
//             );
//         })
//         .catch((error) => {
//             // Handle error here if needed
//             toastr.error("Oops! Something went wrong!");
//             console.error(error);
//         });
// };

const deleteAppointment = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/api/appointments/${id}`).then((response) => {
                appointments.value = appointments.value.filter(
                    (appointment) => appointment.id !== id
                );
                Swal.fire(
                    "Deleted!",
                    "Appointment has been deleted.",
                    "success"
                );
            });
        }
    });
};
onMounted(() => {
    getAppointments();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">List Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Starter Page</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <router-link to="/admin/create-appointment">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Add
                                    New Appointment
                                </button>
                            </router-link>
                        </div>
                        <div class="btn-group">
                            <button
                                @click="getAppointments()"
                                type="button"
                                class="btn btn-secondary"
                            >
                                <span class="mr-1">All</span>
                                <span class="badge badge-pill badge-info">{{
                                    appointments.length
                                }}</span>
                            </button>

                            <button
                                @click="
                                    getAppointments(appointmentStatus.scheduled)
                                "
                                type="button"
                                class="btn btn-default"
                            >
                                <span class="mr-1">Scheduled</span>
                                <span class="badge badge-pill badge-primary">{{
                                    appointments.length
                                }}</span>
                            </button>

                            <button
                                @click="
                                    getAppointments(appointmentStatus.confirmed)
                                "
                                type="button"
                                class="btn btn-default"
                            >
                                <span class="mr-1">Confirmed</span>
                                <span class="badge badge-pill badge-success">{{
                                    appointments.length
                                }}</span>
                            </button>

                            <button
                                @click="
                                    getAppointments(appointmentStatus.cancelled)
                                "
                                type="button"
                                class="btn btn-default"
                            >
                                <span class="mr-1">Cancelled</span>
                                <span class="badge badge-pill badge-danger">{{
                                    appointments.length
                                }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Client Name</th>
                                        <th width="20%" scope="col">Title</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody v-if="appointments.length > 0">
                                    <tr
                                        v-for="(
                                            appointment, index
                                        ) in appointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            {{ appointment.client.firstname }}
                                            {{ appointment.client.lastname }}
                                        </td>
                                        <td>{{ appointment.title }}</td>
                                        <td>{{ appointment.start_time }}</td>
                                        <td>{{ appointment.end_time }}</td>
                                        <td>
                                            <span
                                                :class="
                                                    getBadgeClass(
                                                        appointment.status
                                                    )
                                                "
                                            >
                                                {{
                                                    getAppointmentStatusText(
                                                        appointment.status
                                                    )
                                                }}
                                            </span>
                                        </td>
                                        <td>
                                            <router-link
                                                :to="`/admin/appointment/${appointment.id}/edit`"
                                                ><i class="fa fa-edit mr-2"></i
                                            ></router-link>
                                            <a
                                                href="#"
                                                @click.prevent="
                                                    deleteAppointment(
                                                        appointment.id
                                                    )
                                                "
                                                ><i
                                                    class="fa fa-trash text-danger"
                                                ></i
                                            ></a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="6">
                                            <p class="text-center">
                                                Oops! No record found to
                                                dispaly...
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
