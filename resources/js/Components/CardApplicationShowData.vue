<template>
    <div v-if="application" class="row">

        <div class=" col-12  col-md-6 col-lg-5">

            <header class="row">
                    <h4 class="text-left mt-3">{{ $t('application') }}: {{ application.id }}</h4>
                <button v-if="isCheckingByUser" :aria-label="$t('save')"
                        class="btn btn-secondary" @click="changeStatus($enums.CardStatusEnum.TEMPORARY_CHECKED)">
                    <i aria-hidden="true" class="bi bi-save"></i>
                    <span class="visually-hidden">{{ $t('save') }}</span>
                </button>
                <button v-else :aria-label="$t('edit')" class="btn btn-secondary"
                        @click="changeStatus($enums.CardStatusEnum.CHECKING)">
                    <i aria-hidden="true" class="bi bi-pencil"></i>
                    <span class="visually-hidden">{{ $t('edit') }}</span>
                </button>
            </header>
            <card-applicant-info :model="application"/>

            <div class="">
                <h4>{{ $t('applicationStatus') }}</h4>
                <div>
                    <label>{{ $t('comment.latestFrom') }} {{
                            (application.card_last_update.card_application_staff_id ? $t('staff') : $t('applicant')).toLowerCase()
                        }}:</label>
                    <p>{{ application.card_last_update.comment ?? $t('comment.value', 0) }}</p>
                </div>
                <div>
                    <label for="commentStaff">{{ $t('comment.enter') }}</label>
                    <input id="commentStaff" v-model="commentChecking"
                           :disabled="!isCheckingByUser" class="form-control mb-2" type="text">
                    <label for="expiration_date">{{ $t('expiration date') }}</label>
                    <input id="expiration_date" v-model="expirationDate" :disabled="!isCheckingByUser"
                           class="form-control mb-2" type="date">
                </div>
                <div v-if="isCheckingByUser" aria-label="Status buttons" class="btn-group mb-2" role="group">
                    <button :class="{ active: application.card_last_update.status === $enums.CardStatusEnum.ACCEPTED }"
                            class="btn btn-outline-primary" type="button"
                            @click="changeStatus($enums.CardStatusEnum.ACCEPTED)">
                        {{ $t('status.accepted') }}
                    </button>
                    <button :class="{ active: application.card_last_update.status === $enums.CardStatusEnum.REJECTED }"
                            class="btn btn-outline-secondary" type="button"
                            @click="changeStatus($enums.CardStatusEnum.REJECTED)">
                        {{ $t('status.rejected') }}
                    </button>
                    <button
                        :class="{ active: application.card_last_update.status === $enums.CardStatusEnum.INCOMPLETE }"
                        class="btn btn-outline-warning" type="button"
                        @click="changeStatus($enums.CardStatusEnum.INCOMPLETE)">
                        {{ $t('status.incomplete') }}
                    </button>
                </div>
                <message v-bind="result"></message>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-7">
            <div class="row">
                <h5 class="mt-3 col-auto">{{ $t('file', 2) }}</h5>
                <div class="mb-3 col-auto">
                    <select v-model="selectFile" :aria-label="$t('select_file')" class="form-select">
                        <option v-for="file in files" :key="file.id" :value="file">{{ $t('file') }}: {{
                                file.id
                            }}
                        </option>
                    </select>
                </div>
                <div v-if="isCheckingByUser && selectFile" class="mb-3 col-auto">
                    <select v-model="selectFile.status" :aria-label="$t('file_status')" class="form-select"
                            @change="updateDocumentStatus(selectFile)">
                        <option disabled value="">{{ $t('pleaseSelect') }}</option>
                        <option v-for="(value, status) in $enums.CardDocumentStatusEnum" :key="status" :value="value">
                            {{ $t('status.' + status.toLowerCase()) }}
                        </option>
                    </select>
                </div>
                <message v-bind="resultFile"></message>
            </div>
            <object v-if="selectFile" :data="selectedFileUrl" class="pdf-object" type="application/pdf"></object>
        </div>
    </div>
</template>

<script>
import ModelsToTable from "../components/modelsToTable.vue";
import CardApplicantInfo from "./cardApplicantInfo.vue";
import {mapGetters} from "vuex"

export default {
    components: {CardApplicantInfo, ModelsToTable},
    props: {
        application: Object
    },
    data() {
        return {
            resultFile: {
                message: '',
                success: null,
                hide: true,
                errors: []
            },
            currentStatus: null,
            currentFileStatus: null,
            selectFile: null,
            files: [],
            commentChecking: null,
            expirationDate: null,
            result: {
                message: '',
                success: true,
                hide: false,
                errors: []
            }
        };
    },
    methods: {
        startingData() {
            this.currentStatus = this.application.card_last_update.status;
            this.files = this.application.card_application_document;
            this.expirationDate = this.application.expiration_date;
        },
        async updateDocumentStatus(file) {
            let params = new FormData();
            let url = this.route('document.update', {'document': file.id});
            this.resultFile.message = '';
            params.append('_method', 'PUT');
            params.append('status', file.status);
            try {
                const response = await this.$axios.post(url, params);
                let json = response.data;
                this.resultFile.success = json.success;
                this.resultFile.message = json.message;
                this.resultFile.errors = [];
                return json.success;
            } catch (errors) {
                this.resultFile.success = false;
                this.resultFile.errors = errors.response.data.errors;
                this.resultFile.message = this.$t("request_failed");
                return false;
            }
        },
        async changeStatus(status) {
            this.application.card_last_update.status = status;
            await this.updateApplicationStatus(this.application);
        },
        updateApplicationStatus(application) {
            let params = new FormData();
            params.append('status', application.card_last_update.status);
            params.append('card_application_id', application.id);
            if (this.expirationDate && application.card_last_update.status === this.$enums.CardStatusEnum.ACCEPTED) {
                params.append('expiration_date', this.expirationDate);
            }
            if (this.commentChecking) {
                params.append('card_application_staff_comment', this.commentChecking);
            }
            this.$axios.post(this.route('cardApplication.checking.store', {'category': application.card_last_update.status}), params)
                .then(response => {
                    let json = response.data;
                    this.result.success = json === 1;
                    this.result.errors = [];
                    if (this.result.success) {
                        this.result.message = this.$t('changeFromTo', {
                            'from1': `status.${this.currentStatus.replace(' ', '_')}`,
                            'to1': `status.${application.card_last_update.status.replace(' ', '_')}`
                        });
                        // `Change from ${this.currentStatus} ${this.$t("to").toLowerCase()} ${application.card_last_update.status}`;
                        this.currentStatus = application.card_last_update.status;
                    } else {
                        application.card_last_update.status = this.currentStatus;
                        this.result.message = this.$t("request_failed");
                    }
                })
                .catch(errors => {
                    this.result.message = this.$t("request_failed");
                    this.result.errors = errors.response.data.errors;
                    this.result.success = false;
                    application.card_last_update.status = this.currentStatus;
                });
        }
    },
    computed: {
        selectedFileUrl() {
            return this.route('document.show', {'document': this.selectFile?.id});
        },
        ...mapGetters('auth', [
            'currentUser',
        ]),
        isCheckingByUser() {
            return this.application.card_last_update.status === this.$enums.CardStatusEnum.CHECKING
                && this.application.card_last_update.card_application_staff_id === this.currentUser.id;
        }
    },
    watch: {
        application(newValue) {
            if (newValue) this.startingData();
            this.selectFile = null;
        },
        async selectFile(newValue, oldValue) {
            if (oldValue && oldValue.status !== this.currentFileStatus) {
                if (!await this.updateDocumentStatus(oldValue)) {
                    oldValue.status = this.currentFileStatus;
                }
            }
            this.currentFileStatus = newValue ? newValue.status : null;
        }
    },
    created() {
        if (this.application) this.startingData();
    }
};
</script>

<style scoped>
.pdf-object {
    height: 500px;
    width: 100%;
    border: 1px solid #ccc;
}
</style>
