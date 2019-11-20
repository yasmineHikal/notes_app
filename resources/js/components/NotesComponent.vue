<template>
    <div class="card">
        <div class="card-header">My Notes</div>
        <div class="card-body">
            <button type="button" class="mb-3 btn btn-primary" @click="newModel()">
                Add Note
            </button>
            <ul class="list-group">
                <li class="list-group-item" v-for="note in notes.data" :key="note.id">

                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{note.title}}</h5>
                        <small>{{note.created_at}}</small>
                    </div>


                    <div class="float-right">
                        <a href="#" @click="editModel(note)">
                            <i class="fa fa-edit"></i>
                        </a>
                        /
                        <a href="#" @click="deleteNote(note.id)">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <p class="mb-1">{{note.description}}</p>

                </li>
            </ul>
            <br/>
            <pagination :data="notes" @pagination-change-page="getResults"></pagination>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="AddNewNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form @submit.prevent="editMode ? updateNote() : createNote()">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editMode" id="exampleModalCenterTitle">Create Note</h5>
                            <h5 class="modal-title" v-show="editMode" id="exampleModalCenterTitle2">Update Note</h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.title" type="text" name="title"
                                       placeholder="Title"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                <has-error :form="form" field="title"></has-error>
                            </div>
                            <div class="form-group">
                        <textarea v-model="form.description" name="description" id="description"
                                  placeholder="Enter Description"
                                  class="form-control"
                                  :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                <has-error :form="form" field="description"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" v-show="editMode" class="btn btn-success">Update Note</button>
                            <button type="submit" v-show="!editMode" class="btn btn-primary">Create Note</button>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editMode : true,
                notes : {},
                form: new Form({
                    id : '',
                    title : '',
                    description : '',
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/note?page=' + page)
                    .then(response => {
                        this.notes = response.data;
                    });
            },
            updateNote() {
                this.$Progress.start();
                this.form.put('api/note/' + this.form.id)
                    .then(() => {
                        Fire.$emit('afterCreate');
                        $('#AddNewNote').modal('hide')
                        this.$Progress.finish();
                        Swal.fire(
                            'Updated!',
                            'Note data has been Updated.',
                            'success'
                        )
                    }).catch(() => {
                    this.$Progress.fail();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    })
                })

            },
            editModel(note) {
                this.editMode = true;
                this.form.reset();
                $('#AddNewNote').modal('show')
                this.form.fill(note);
            },
            newModel() {
                this.editMode = false;
                this.form.reset();
                $('#AddNewNote').modal('show')
            },
            deleteNote(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        //send request
                        this.form.delete('api/note/' + id).then(() => {
                            Swal.fire(
                                'Deleted!',
                                'Your note has been deleted.',
                                'success'
                            )
                        }).catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            })
                        })

                        Fire.$emit('afterCreate');

                    }
                })
            },
            createNote() {
                this.$Progress.start();
                this.form.post('api/note')
                    .then(() => {
                            Fire.$emit('afterCreate');
                            Fire.$emit('sendNotification');
                            $('#AddNewNote').modal('hide')
                            this.$Progress.finish();
                            Toast.fire({
                                icon: 'success',
                                title: 'Note created successfully'
                            })
                        }
                    )
                    .catch((err) => {
                        this.$Progress.fail();
                        console.log(err)
                    });
            },
            loadNotes() {
                axios.get('api/note')
                    .then(({data}) => {
                        this.notes = data
                    })
            }
        },
        created() {
            this.loadNotes();
            this.getResults();
            Fire.$on('afterCreate', () => {
                this.loadNotes();
            })

        },
    }
</script>
