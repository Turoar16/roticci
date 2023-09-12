<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#client').select2()//inicializador
        // capturamos values when change event
        $('#client').on('change', function(e) {
            var cId = $('#client').select2("val")//get client id
            var cName = $('#client option:selected').text()//get client name
            @this.set('clientSelectedId', cName)//set client id selected
            @this.set('clientSelectedName', cId)//set client name selected
            $('#nombre').val(cId);
        });
        console.log('Client ready!');
    });

</script>