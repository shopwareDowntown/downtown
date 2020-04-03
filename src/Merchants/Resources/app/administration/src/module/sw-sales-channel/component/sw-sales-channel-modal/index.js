
const { Component } = Shopware;

Component.override('sw-sales-channel-modal', {
    methods: {
        onAddChannel(id) {
            this.onCloseModal();

            this.$nextTick(() => {
                if (id) {
                    this.$router.push({name: 'sw.sales.channel.create', params: {typeId: id}});
                }
            });
        },
    }
});
