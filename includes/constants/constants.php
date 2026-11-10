<?php
/* Nenu Adrian Mircea 2012+ */
	// Machine-local overrides (e.g. putenv() SMTP_* settings). Not tracked in git.
	if (is_file(__DIR__ . '/../local_settings.php')) {
		require __DIR__ . '/../local_settings.php';
	}
	return [
		// Base URL for links and assets. Auto-detected from the request unless
		// APP_URL is set (recommended behind a reverse proxy / HTTPS), e.g.
		// APP_URL=https://game.example.com/
		'url' => getenv('APP_URL')
			? rtrim(getenv('APP_URL'), '/') . '/'
			: (($_SERVER['REQUEST_SCHEME'] ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http')) . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME'])),
		'contact_email' => 'undefined@undefined.com',
		
		'tutorialSteps' => 20,

		'gridBrowseZone' => 5,
		'gridBrowseNode' => 0.1,

		'gridClustersPerZone' => 1000,

		'newMessageDataPoints' => 10,
		'newMessageReplyDataPoints' => 3,

		'ac_bonus_when_above' => 100,
		'ac_bonus_percent' => 20,
		"trainEvery" => 10*60*60,
		'timeBetweenJobs' => 12 * 60 * 60,

		// Secret for /cron/key1/<cron_key>/<job>/1 URLs. Set CRON_KEY in production:
		// the default below is public in the source code.
		'cron_key' => getenv('CRON_KEY') ?: 'MDMwN2Q3OGRiYmM4Y2RkOWZjNTBmMzA4MzViZDZiNjQ=',

		// reCAPTCHA (optional): https://www.google.com/recaptcha/admin/create
		'recaptcha_site_key' => getenv('RECAPTCHA_SITE_KEY') ?: '',
		'recaptcha_secret_key' => getenv('RECAPTCHA_SECRET_KEY') ?: '',
		
		// SMTP is disabled by default (empty host). Enable by setting the SMTP_HOST
		// environment variable, e.g. SMTP_HOST=127.0.0.1 for local sendmail/postfix.
		"smtp_host" => getenv('SMTP_HOST') ?: '',
		"smtp_username" => getenv('SMTP_USERNAME') ?: "",
		"smtp_password" => getenv('SMTP_PASSWORD') ?: "",
		"smtp_name" => "Secret Republic",
		"smtp_from" => getenv('SMTP_FROM') ?: "undefined@undefined.com",
		"smtp_secure" => getenv('SMTP_SECURE') ?: '', // '' plain | 'tls' | 'ssl'
		"smtp_port" =>(int)(getenv('SMTP_PORT') ?: 25),


	  	"gridNodeSize" => 10,
		"worldBirth" => 1395530364,


		"max_tasks"=>3,
		"defaultGroup"=>2,

		"detention_bail"=>2,


		"captcha_check"=>60*15,
		"connection_time"=>10,


		//CONNECT FEATURE

		"energy_hour"=>10,



		"blog_title_size"=>50,
		"blog_article_size"=>8888,
		"forum_post_size"=>6666,


		"org_application_size"=>500,


		"nra_page"=>10,
		"nrc_page"=>10,



	];
