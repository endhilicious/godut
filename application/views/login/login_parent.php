  <?php echo validation_errors(); ?>
<?php echo form_open('parent_controller/login_parent'); ?>
           <input type="text" class="form-control" name="no_identitas" placeholder="No Identitas" required="" autofocus="" /><br /><br />

            <input type="password" name="password" placeholder="Password" required=""/> 
            <button class="btn btn-lg btn-primary btn-block" type="submit"  value=" Login " name="submit">Login</button>
<?php echo form_close(); ?>