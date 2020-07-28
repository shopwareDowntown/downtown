import template from './sw-profile-index.html.twig';

if (Shopware.Service('swagSecurityState').isActive('NEXT-9242')) {
    Shopware.Component.override('sw-profile-index', {
        template,

        data() {
            return {
                confirmPasswordModal: false
            };
        },

        computed: {
            confirmPassword: {
                get() {
                    return this.oldPassword;
                },
                set(value) {
                    this.oldPassword = value;
                }
            }
        },
        methods: {
            onSave() {
                if (this.checkEmail() === false) {
                    return;
                }
                if (this.checkPassword() === false) {
                    return;
                }

                this.confirmPasswordModal = true;
            },
            checkEmail() {
                if (!this.user.email && !email(this.user.email)) {
                        this.createErrorMessage(this.$tc('sw-profile.index.notificationInvalidEmailErrorMessage'));

                        return false;
                    }
                    return true;
            },
            checkPassword() {
                if (this.newPassword && this.newPassword.length > 0) {
                    if (this.oldPassword === this.newPassword) {
                        this.createErrorMessage(this.$tc('sw-profile.index.notificationNewPasswordIsSameAsOldErrorMessage'));
                        return false;
                    }

                    if (this.newPassword !== this.newPasswordConfirm) {
                        this.createErrorMessage(this.$tc('sw-profile.index.notificationPasswordErrorMessage'));
                        return false;
                    }

                    this.user.password = this.newPassword;

                    return true;
                }
            },
            verifyUserToken() {
                const { username } = Shopware.State.get('session').currentUser;

                return this.loginService.verifyUserByUsername(username, this.confirmPassword).then(({ access }) => {
                    this.confirmPassword = '';
                    if (typeof access === 'string') {
                        return access;
                    }

                    return false;
                }).catch(() => {
                    this.confirmPassword = '';
                    this.createErrorMessage(this.$tc('sw-profile.index.notificationOldPasswordErrorMessage'));

                    return false;
                });
            },
            saveUser(authToken) {
                const context = { ...Shopware.Context.api };
                context.authToken.access = authToken;

                if (this.newPassword && this.newPassword.length) {
                    this.user.password = this.newPassword;
                }

                this.userRepository.save(this.user, context).then(() => {
                    this.$refs.mediaSidebarItem.getList();

                    if (Shopware.Service('localeHelper')) {
                        Shopware.Service('localeHelper').setLocaleWithId(this.user.localeId);
                    }

                    if (this.newPassword) {
                        // re-issue a valid jwt token, as all user tokens were invalidated on password change
                        this.loginService.loginByUsername(this.user.username, this.newPassword).then(() => {
                            this.isLoading = false;
                            this.isSaveSuccessful = true;
                        }).catch(() => {
                            this.handleUserSaveError();
                        });
                    } else {
                        this.isLoading = false;
                        this.isSaveSuccessful = true;
                    }

                    this.oldPassword = '';
                    this.newPassword = '';
                    this.newPasswordConfirm = '';
                }).catch((err) => {
                    this.handleUserSaveError();
                });
            },

            async onSubmitConfirmPassword() {
                const verifiedToken = await this.verifyUserToken();

                if (!verifiedToken) {
                    return;
                }

                this.confirmPasswordModal = false;
                this.isSaveSuccessful = false;
                this.isLoading = true;

                this.saveUser(verifiedToken);
            },

            onCloseConfirmPasswordModal() {
                this.confirmPassword = '';
                this.confirmPasswordModal = false;
            },
        }
    })
}
