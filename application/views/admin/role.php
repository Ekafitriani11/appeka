<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    

    <div class="row">
        <div class="col-lg-6">
            <!--pesan error-->
            <?= form_error(
                'menu',
                '<div class="alert alert-succes" role="alert">
                </div>'
            ); ?>
            <!-- <div class="flash-data" data=flashdata="<?=$this->session->flashdata('message'); ?>-->
            <?=$this->session->flashdata('message'); ?>
            <!--akhir pesan error-->

            <!--tombol tembah-->
            <a href="" class="btn btn-primary mb-3" class="btn btn-primary"
            data-toggle="modal"
            data-target="#Addnewrole">Add New Role</a>
            <!--Tabel-->
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($role as $r) : ?>
                        <tr>
                            <th scope="row"><?= $i; ?></th>
                            <td><?= $r['role']; ?></td>
                            <td>
                                <a href="<?= base_url('admin/roleaccess/') . $r['id'] ?>"class="badge badge-warning badge-sm">Access</a>
                                <a href="#" class="badge badge-success" data-toggle="modal" data-popup="tooltip" data-placement="top" title="edit" data-target="#roleedit<?= $r['id'] ?>">Edit</a>
                                <a href="<?= base_url('admin/hapusrole/') . $r['id'] ?>" onclick="return confirm('Yakin mau dihapus?');" class="badge badge-danger badge-sm tombol-hapus">Delete</a>
                                <!-- <a href="#" onclick="hapusRole('')" class="badge badge-danger badge-sm tombol-hapus">Delete</a> -->
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!--akhir tabel -->
        </div>
    </div>

</div>
<!-- / .container-fluid -->

</div>
<!-- End of Main Content -->



<!--Button tringger modal -->


<!--Modal-->
<div class="modal fade" id="Addnewrole" tabindex="1" aria-labelledby="AddnewroleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddnewroleLabel">Add new Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/role'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="role"
                        name="role" placeholder="Role Name">
                    </div>
                    </div>

                    <div class="modal-footer">
                        <button typpe="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div> 
        </div> 
    </div> 


    <!--Modal edit-->
    <?php foreach($role as $r) : ?>
<div class="modal fade" id="roleedit<?= $r['id']; ?>" tabindex="-1" aaria-labelledby="roleedit" aria-hidden="true" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleeditLabel">Edit Role</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"></button>
                <span aria-hidden="true">&times;</span>
            </div>
            <form action="<?= base_url('admin/editrole/'); ?>" method="post">

            <input type="text" value="<?= $r['role']?>" class="form-control" id="role" name="role" placeholder="Menu Name" >
                
                <div class="modal-body">
                    <div class="form-group">
                    <input type="hidden" class="form-control" readonly value="<?= $r['id'] ?>" name="id">
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button typpe="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-fw fa-pencil-alt fa-sm"></i>Edit</button>
                    </div>
                </form>
            </div> 
        </div> 
    </div> 

<?php endforeach; ?>