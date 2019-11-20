<template>

    <li class="nav-item dropdown">
        <a href="#" id="navbarDropdown" class="nav-link" data-toggle="dropdown" role="button" aria-expanded="false">
            <i class="fa fa-bell"></i>
            <span class="glyphicon glyphicon-globe"></span>
            Notifications <span class="badge">{{notifications.length}}</span> <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
            <li v-for="notification in notifications">
                <a href="#">
                    SomeOne create new note<br>
                    <small> "" {{ notification.data.data.title }} ""</small>
                </a>
            </li>
            <li v-if="notifications.length == 0">
                There is no new notifications
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data: () => ({
            user:{},
            notifications: ''
        }),
        created() {
            axios.get("api/auth-user")
                .then(({ data }) => {
                    this.user = data
                })
                .catch((err)=>{
                    console.log(err)
                });
            axios.post('api/notification/get').then(response => {
                this.notifications = response.data;
                console.log(response.data)

            });
            var userId = $('meta[name="userId"]').attr('content');
            Echo.channel('App.User.' + userId)
                .listen('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (notification) => {
                    this.notifications.push(notification);
                });
        }
    }
</script>
