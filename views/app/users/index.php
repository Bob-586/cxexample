<?php

/**
 * @copyright (c) 2015
 * @author Chris Allen, Robert Strutts
 */

?>
<h1 id="UserListHeader">Users Account List</h1>
<button class="btn btn-success btn-sm" name="add_user" type="button" value="Add New User" onclick="document.location='<?php echo $this->get_url('/app/users', 'edit_user', 'id=0'); ?>'"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Add New User</button>
        <div class="wrapper"><div class="container"><div class="row">

<div class="panel panel-default top20">
<div class="panel-body">
<table id="UserList" class="table table-striped table-hover">
  <thead>
    <tr>
			  <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
				<th>Username</th>        
        <th>Rights</th>
		</tr>
  </thead>
  <tbody>
                    
  </tbody>
</table>
</div>
</div>

        </div></div></div>