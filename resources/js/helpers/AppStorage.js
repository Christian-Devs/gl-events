class AppStorage {

    storeToken(token) {
        localStorage.setItem('token', token);
    }

    storeUser(user) {
        localStorage.setItem('user', user);
    }

    storeRole(role) {
        localStorage.setItem('role', role);
    }

    store(token, user, role = null) {
        this.storeToken(token)
        this.storeUser(user)
        if (role) this.storeRole(role)
    }

    clear() {
        localStorage.removeItem('token', token);
        localStorage.removeItem('user', user);
        localStorage.removeItem('role', role);
    }

    getToken() {
        return localStorage.getItem('token');
    }

    getUser() {
        return localStorage.getItem('user');
    }

    getRole() {
        return localStorage.getItem('role');
    }
}

export default AppStorage = new AppStorage();