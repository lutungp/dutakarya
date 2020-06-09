<link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdata.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.sort.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.pager.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.selection.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxgrid.edit.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxnumberinput.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxwindow.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>/assets/plugins/jqwidgets/scripts/demos.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo BASE_URL ?>/controllers/C_usergroup.php?action=getusergroup";
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'usergroup_id', type: 'int' },
                { name: 'usergroup_kode', type: 'string' },
                { name: 'usergroup_nama', type: 'string' },
                { name: 'usergroup_aktif', type: 'string' }
            ],
            id: 'user_id',
            url: url,
            updaterow: function (rowid, rowdata, commit) {
                // synchronize with the server - send update command
                // call commit with parameter true if the synchronization with the server is successful 
                // and with parameter false if the synchronization failder.
                commit(true);
            }
        };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) {},
            loadError: function (xhr, status, error) { }
        });
        // initialize jqxGrid
        $("#grid").jqxGrid(
        {
            width: '100%',
            source: dataAdapter,                
            pageable: true,
            autoheight: true,
            sortable: true,
            altrows: true,
            enabletooltips: true,
            editable: true,
            selectionmode: 'multiplecellsadvanced',
            columns: [
                { text: 'Group Kode', datafield: 'usergroup_kode'},
                { text: 'Group Nama', datafield: 'usergroup_nama'},
                { text: 'Group Aktif', datafield: 'usergroup_aktif'},
                { text: 'Edit', datafield: 'Edit', columntype: 'button', width:'50', align:'center', sortable:false,
                    cellsrenderer: function () {
                        return "Edit";
                    }, buttonclick: function (row) {
                        
                    }
                },
                { text: 'Delete', datafield: 'Delete', columntype: 'button', width:'50', align:'center', sortable:false,
                    cellsrenderer: function () {
                        return "Delete";
                    }, buttonclick: function (row) {
                        
                    }
                }
            ]
        });

    });
</script>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body default">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm" onclick="addUserGroup()">Tambah</button>
                    <div id='jqxWidget' style="margin-top: 5px;">
                        <div id="grid"></div>
                        <div id="cellbegineditevent"></div>
                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function addUserGroup() {
        
    }
</script>