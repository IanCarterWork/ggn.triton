<?php


	$GGN->{'HEADER:CONTENT:TYPE'} = 'text/html';

	$GGN->{'Infos:Name'} = 'GGN Triton';

	$GGN->{'Infos:Version'} = '0.0.2';


	$GGN->{'Error:Reporting'} = TRUE;

	$GGN->{'Autonomous:Connect'} = FALSE;

	$GGN->{'Kernel:Default'} = 'kernel-0.0.4';

	$GGN->{'Http:Host'} = NULL;
	
	$GGN->{'Http:Current:URL'} = NULL;


	$GGN->{'ClientDevice:Cookie:iD'} = 'GGNClientiD';

	$GGN->{'ClientDevice:Cookie:Duration'} = 60*60*24*367;


	$GGN->{'Client:Session:Login'} = 'GGN.Connect.Session.Login';

	$GGN->{'Client:Session:Duration'} = 60*60*24*30;

	
	$GGN->{'Autonomous:Connect:Password:Encryption'} = 'PASSWORD:HASH';





	/**
	 * 
	 * Architecture Dossiers
	 * @Version 0.1.2
	 */
	
	$GGN->{'Dir:Main'} = dirname(__FILE__) . '/';

		$GGN->{'Dir:Settings'} = $GGN->{'Dir:Main'} . 'settings/';
		
		$GGN->{'Dir:Logs'} = $GGN->{'Dir:Main'} . 'logs/';

		$GGN->{'Dir:Sessions'} = $GGN->{'Dir:Main'} . 'sessions/';

		$GGN->{'Dir:Components'} = $GGN->{'Dir:Main'} . 'components/';

			$GGN->{'Dir:Services'} = $GGN->{'Dir:Components'} . 'Service/';


	$GGN->{'Dir:Root'} = dirname( $GGN->{'Dir:Main'} ) . '/';

		$GGN->{'Dir:Caches'} = $GGN->{'Dir:Root'} . '.caches/';
		
		$GGN->{'Dir:Public'} = $GGN->{'Dir:Root'} . 'public_html/';


			$GGN->{'Dir:Viewer'} = $GGN->{'Dir:Public'} . 'viewer/';

			$GGN->{'Dir:Assets'} = $GGN->{'Dir:Public'} . 'assets/';

				$GGN->{'Dir:Themes'} = $GGN->{'Dir:Assets'} . 'themes/';

				$GGN->{'Dir:CSS'} = $GGN->{'Dir:Assets'} . 'css/';

				$GGN->{'Dir:Images'} = $GGN->{'Dir:Assets'} . 'images/';

				$GGN->{'Dir:Videos'} = $GGN->{'Dir:Assets'} . 'videos/';

				$GGN->{'Dir:Documents'} = $GGN->{'Dir:Assets'} . 'documents/';

				$GGN->{'Dir:UiKits'} = $GGN->{'Dir:Assets'} . 'uikits/';


	$GGN->{'Dir:Apps'} = $GGN->{'Dir:Root'} . 'apps/';
		


	// $GGN->{'Dir:Framework:CSS'} = $GGN->{'Dir:Components'} . 'Framework/CSS/';

	// $GGN->{'Dir:Framework:UiColoring'} = $GGN->{'Dir:Components'} . 'Framework/UiColoring/';

	// $GGN->{'Dir:Framework:UiLayout'} = $GGN->{'Dir:Components'} . 'Framework/UiLayout/';

	// $GGN->{'Dir:Framework:UiSense'} = $GGN->{'Dir:Components'} . 'Framework/UiSense/';



?>