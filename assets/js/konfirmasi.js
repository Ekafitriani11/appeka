function hapusMenu(url){

    Swal.fire({
        title: 'Are you sure?',
            text: "Yakin ingin hapus menu?",
            icon: 'Warning',
            showCancelButton: true,
            confirmbuttonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmbuttonText: 'Ya, hapus!'
    }).then((result) => {
        if(result.value) {
            document.location.href = url;
        }
    })
}

function hapusRole(url) {

    Swal.fire({
        title: 'are you sure?',
              text: "Yakin ingin hapus role",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, hapus!'
      }).then((result) => {
          if (result.value) {
              document.location.href = url;
          }
      })
}
function hapusSubmenu(url){
    
    Swal.fire({
        title: 'Are you sure?',
            text: "Yakin ingin hapus sub menu?",
            icon: 'Warning',
            showCancelButton: true,
            confirmbuttonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmbuttonText: 'Ya, hapus!'
    }).then((result) => {
        if(result.value) {
            document.location.href = url;
        }
    })
}