  <?php echo validation_errors(); ?>
<?php echo form_open('guru_controller/login_guru'); ?>
           <input type="text" class="form-control" name="nign" placeholder="NIGN" required="" autofocus="" /><br /><br />

            <input type="password" name="password" placeholder="Password" required=""/> 
            <button class="btn btn-lg btn-primary btn-block" type="submit"  value=" Login " name="submit">Login</button>
<?php echo form_close(); ?>