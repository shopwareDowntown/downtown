import template from './sw-settings-user-detail.html.twig';

if (Shopware.Service('swagSecurityState').isActive('NEXT-9242')) {
    Shopware.Component.override('sw-settings-user-create', {
        template,
        methods: {
            onSave() {
                if (!this.user.localeId) {
                    this.user.localeId = this.currentUser.localeId;
                }
                this.confirmPasswordModal = true;
            },
        }
    })

    Shopware.Component.override('sw-settings-user-detail', {
        template,
        data() {
            return {
                confirmPasswordModal: false,
                confirmPassword: ''
            };
        },

        methods: {
            onSave() {
                this.confirmPasswordModal = true;
            },

            saveUser(authToken) {
                this.isSaveSuccessful = false;
                this.isLoading = true;
                let promises = [];

                if (this.currentUser.id === this.user.id) {
                    promises = [Shopware.Service('localeHelper').setLocaleWithId(this.user.localeId)];
                }

                return Promise.all(promises).then(this.checkEmail().then(() => {
                    if (!this.isEmailUsed) {
                        this.isLoading = true;
                        const titleSaveError = this.$tc('sw-settings-user.user-detail.notification.saveError.title');
                        const messageSaveError = this.$tc(
                            'sw-settings-user.user-detail.notification.saveError.message', 0, { name: this.fullName }
                        );

                        const context = { ...Shopware.Context.api };
                        context.authToken.access = authToken;

                        return this.userRepository.save(this.user, context).then(() => {
                            this.isLoading = false;
                            this.isSaveSuccessful = true;
                        }).catch((exception) => {
                            this.createNotificationError({
                                title: titleSaveError,
                                message: messageSaveError
                            });
                            this.isLoading = false;
                            throw exception;
                        });
                    }
                    return Promise.resolve();
                }).finally(() => {
                    this.isLoading = false;
                }));
            },

            async onSubmitConfirmPassword() {
                const verifiedToken = await this.verifyUserToken();

                if (!verifiedToken) {
                    return;
                }

                this.confirmPasswordModal = false;
                this.saveUser(verifiedToken);
            },

            onCloseConfirmPasswordModal() {
                this.confirmPassword = '';
                this.confirmPasswordModal = false;
            },

            verifyUserToken() {
                const { username } = Shopware.State.get('session').currentUser;

                return Shopware.Service('loginService').verifyUserByUsername(username, this.confirmPassword).then(({ access }) => {
                    this.confirmPassword = '';

                    if (typeof access === 'string') {
                        return access;
                    }

                    return false;
                }).catch(() => {
                    this.confirmPassword = '';
                    this.createNotificationError({
                        title: this.$tc('sw-settings-user.user-detail.passwordConfirmation.notificationPasswordErrorTitle'),
                        message: this.$tc('sw-settings-user.user-detail.passwordConfirmation.notificationPasswordErrorMessage')
                    });

                    return false;
                });
            }
        }
    })
}
