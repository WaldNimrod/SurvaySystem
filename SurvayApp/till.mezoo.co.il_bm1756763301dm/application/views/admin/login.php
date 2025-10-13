<div class="row">
    <div class="col-sm-4 col-sm-offset-4">
        <br/><br/>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Login:</h4>
                <?php
                if ($error) {
                    echo 'Incorrect login details.<br/><br/>';
                }
                ?>
                <form method="POST" action="">
                    Username:<br/>
                    <input type="text" name="username" placeholder="Username..." class="form-control" value=""/>
                    <br/>
                    Password:<br/>
                    <input type="password" name="password" placeholder="Password..." class="form-control"
                           value=""/>
                    <br/>
                    <input type="submit" class="btn btn-default" value="Submit"></input>
                </form>
            </div>
        </div>
    </div>
</div>