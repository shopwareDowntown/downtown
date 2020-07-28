class State {
    isActive(ticket) {
        return Shopware.State.get('context').app.config.swagSecurity.includes(ticket);
    }
}

export default State;
