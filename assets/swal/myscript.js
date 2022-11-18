const flashData = $('.flash-data').data('flashdata');
if (flashData) {
    Swal({
        title: 'Data login' + flashData,
        text: 'disimpan',
        type: 'success'
    })
}