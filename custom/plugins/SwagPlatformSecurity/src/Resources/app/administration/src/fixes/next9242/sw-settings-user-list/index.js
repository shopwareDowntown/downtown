import template from './sw-settings-user-list.html.twig';

if (Shopware.Service('swagSecurityState').isActive('NEXT-9242')) {
    Shopware.Component.override('sw-settings-user-list', {
        template,
        data() {
            return {
                confirmPassword: ''
            };
        },

        methods: {
            async onConfirmDelete(user) {
                const username = `${user.firstName} ${user.lastName} `;
                const titleDeleteSuccess = this.$tc('sw-settings-user.user-grid.notification.deleteSuccess.title');
                const messageDeleteSuccess = this.$tc('sw-settings-user.user-grid.notification.deleteSuccess.message',
                    0,
                    { name: username });
                const titleDeleteError = this.$tc('sw-settings-user.user-grid.notification.deleteError.title');
                const messageDeleteError = this.$tc(
                    'sw-settings-user.user-grid.notification.deleteError.message', 0, { name: username }
                );
                if (user.id === this.currentUser.id) {
                    this.createNotificationError({
                        title: this.$tc('sw-settings-user.user-grid.notification.deleteUserLoggedInError.title'),
                        message: this.$tc('sw-settings-user.user-grid.notification.deleteUserLoggedInError.message')
                    });
                    return;
                }

                const verifiedToken = await this.verifyUserToken();

                if (!verifiedToken) {
                    return;
                }

                this.confirmPasswordModal = false;
                const context = { ...Shopware.Context.api };
                context.authToken.access = verifiedToken;

                this.userRepository.delete(user.id, context).then(() => {
                    this.createNotificationSuccess({
                        title: titleDeleteSuccess,
                        message: messageDeleteSuccess
                    });
                    this.getList();
                }).catch(() => {
                    this.createNotificationError({
                        title: titleDeleteError,
                        message: messageDeleteError
                    });
                });
                this.onCloseDeleteModal();
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
    });
}
