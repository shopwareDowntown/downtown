const { Component, Mixin } = Shopware;
import template from './sw-settings-security-view.html.twig';

Component.register('sw-settings-security-view', {
    template,
    inject: ['swagSecurityApi', 'systemConfigApiService'],

    data() {
        return {
            isLoading: true,
            isSaveSuccessful: false,
            confirmPasswordModal: false,
            confirmPassword: '',
            config: {},
            fixes: []
        }
    },

    mixins: [
        Mixin.getByName('notification')
    ],

    methods:{
        onCloseConfirmPasswordModal() {
            this.confirmPasswordModal = false;
            this.isLoading = false;
            this.confirmPassword = '';
        },

        onSave() {
            this.confirmPasswordModal = true;
        },

        onVerifiedSave() {
            this.isLoading = true;

            this.swagSecurityApi.saveValues(this.config, this.confirmPassword).then(() => {
                this.isLoading = true;
                this.confirmPasswordModal = false;
                this.confirmPassword = '';

                this.swagSecurityApi.cacheClear().then(() => {
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                    window.location.reload();
                });
            }).catch(() => {
                this.createNotificationError({
                    title: this.$tc('sw-profile.index.notificationPasswordErrorTitle'),
                    message: this.$tc('sw-profile.index.notificationOldPasswordErrorMessage')
                });
            })
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        }
    },

    async mounted() {
        this.fixes = await this.swagSecurityApi.getFixes();

        for(let fix of this.fixes.availableFixes) {
            this.config[fix] = this.fixes.activeFixes.includes(fix);
        }

        this.isLoading = false;
    }
})
