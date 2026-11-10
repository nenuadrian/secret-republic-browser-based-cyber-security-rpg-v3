{include file="header_home.tpl"}
  <form method="post" autocomplete="off">
		<div class="row-fluid">
		<div class="col-md-3"></div>
			<div class="col-md-6 col-xs-6 nopadding">
				{include file="error_success.tpl"}

				{if $setupConfigured}
				<p>DATABASE CONNECTION FOUND IN includes/database_info.php. CREATE THE ADMIN ACCOUNT TO INSTALL THE GAME.</p>
				{else}
				<select name="DB_DRIVER" id="db-driver">
					<option value="mysql" {if !isset($setupDefaults) || $setupDefaults.driver != 'sqlite'}selected{/if}>MYSQL</option>
					<option value="sqlite" {if isset($setupDefaults) && $setupDefaults.driver == 'sqlite'}selected{/if}>SQLITE (LOCAL FILE)</option>
				</select>
				<input  type="text" placeholder="DB HOST" value="{$smarty.post.DB_HOST|default:$setupDefaults.host|default:'localhost'|escape}" name="DB_HOST" id="db-host" required/>
				<input  type="text" placeholder="DB PORT" value="{$smarty.post.DB_PORT|default:$setupDefaults.port|default:'3306'|escape}" name="DB_PORT" id="db-port" required/>
				<input  type="text" placeholder="DB USER" value="{$smarty.post.DB_USER|default:$setupDefaults.user|default:'root'|escape}" name="DB_USER" id="db-user" required/>
				<input  type="password" placeholder="DB PASS" value="" name="DB_PASS" id="db-pass"/>
				<input  type="text" placeholder="DB NAME (EMPTY DATABASE)" value="{$smarty.post.DB_NAME|default:$setupDefaults.name|default:''|escape}" name="DB_NAME" id="db-name" required/>
				<input  type="text" placeholder="SQLITE PATH (RELATIVE OR ABSOLUTE)" value="{$smarty.post.SQLITE_PATH|default:'includes/local.sqlite'|escape}" name="SQLITE_PATH" id="sqlite-path"/>
				{/if}
				<input  type="text" placeholder="ADMIN USER (4-15 LETTERS/DIGITS)" value="{$smarty.post.ADMIN_USER|default:''|escape}" name="ADMIN_USER" maxlength="15" required/>
				<input  type="password" placeholder="ADMIN PASS (4-20 CHARACTERS)" value="" name="ADMIN_PASS" maxlength="20" required/>
				<input  type="email" placeholder="ADMIN EMAIL" value="{$smarty.post.ADMIN_EMAIL|default:''|escape}" name="ADMIN_EMAIL" required/>
				<br/><br/>
				<button type="submit" style="border-top:0;">SETUP</button>
			</div>
		</div>
	</form>
	{if !$setupConfigured}
	<script>
	(function() {
		var driver = document.getElementById('db-driver');
		var mysqlFields = [
			document.getElementById('db-host'),
			document.getElementById('db-port'),
			document.getElementById('db-user'),
			document.getElementById('db-pass'),
			document.getElementById('db-name')
		];
		var sqliteField = document.getElementById('sqlite-path');

		function refreshFields() {
			var isSqlite = driver.value === 'sqlite';
			for (var i = 0; i < mysqlFields.length; i++) {
				mysqlFields[i].style.display = isSqlite ? 'none' : 'block';
				mysqlFields[i].required = !isSqlite && mysqlFields[i].id !== 'db-pass';
			}
			sqliteField.style.display = isSqlite ? 'block' : 'none';
		}

		driver.addEventListener('change', refreshFields);
		refreshFields();
	})();
	</script>
	{/if}
