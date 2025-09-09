<form id="form_api">
    <div class="row mb-3">
        <div class="col-md-12">
            <input type="text" id="id" name="id" class="form-control">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <input type="file" id="image" name="image" class="form-control">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <input type="text" class="form-control" id="title" name="title">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <textarea name="content" id="content" class="form-control"></textarea>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <button type="button" class="btn btn-info" onclick="ambil_data()">Ambil</button>
            <button type="button" class="btn btn-warning" onclick="update_data()">Update</button>
            <button type="button" class="btn btn-success" onclick="simpan()">Simpan</button>
            <button type="button" class="btn btn-danger" onclick="hapus_data()">Hapus</button>
        </div>
    </div>
</form>

<script>
    function simpan() {
        var form = new FormData($('#form_api')[0]);
        $.ajax({
            type: 'POST',
            url: 'http://localhost:8000/api/posts',
            data: form,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log(response);
            }
        });
    }

    function ambil_data() {
        var id = $('#id').val();

        $.ajax({
            type: 'GET',
            url: 'http://localhost:8000/api/posts/' + id,
            contentType: false,
            processData: false,
            success: function(response) {
                // Skip setting value for file input since it's not possible for security reasons
                $('#title').val(response.data['title']);
                $('#content').val(response.data['content']);
            }
        });
    }

    function update_data() {
        var id = $('#id').val();
        var form = new FormData($('#form_api')[0]);

        // Add _method field to simulate PUT request
        form.append('_method', 'PUT');

        $.ajax({
            type: 'POST', // Use POST with _method field instead of PUT
            url: 'http://localhost:8000/api/posts/' + id,
            data: form,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log(response);
                alert('Data updated successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to update data');
            }
        });
    }

    function hapus_data() {
        var id = $('#id').val();

        $.ajax({
            type: 'DELETE',
            url: 'http://localhost:8000/api/posts/' + id,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response.message);
            }
        });
    }
</script>