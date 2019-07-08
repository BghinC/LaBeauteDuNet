<?php //POUR LE FACEBOOK CONNECT
    require_once 'fbConfig_connexion.php';
    require_once 'user.class.php';

    if(isset($accessToken)){
        if(isset($_SESSION['facebook_access_token'])){
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        }
        else{
            // Put short-lived access token in session
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            
              // OAuth 2.0 client handler helps to manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();
            
            // Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
            $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
            
            // Set default access token to be used in script
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        }
        
        // Redirect the user back to the same page if url has "code" parameter in query string
        if(isset($_GET['code'])){
            header('Location: ./');
        }
        
        // Getting user facebook profile info
        try {
            $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,cover,picture');
            $fbUserProfile = $profileRequest->getGraphNode()->asArray();
        } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            // Redirect user back to app login page
            header("Location: ./");
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        function suppr_accents($str, $encoding='utf-8')
        {//UTILE POUR REMPLACER LES ACCENT ET CARAC SPECIAUX DANS LE NOM ET PRENOM

            // transformer les caractères accentués en entités HTML
            $str = htmlentities($str, ENT_NOQUOTES, $encoding);
         
            // remplacer les entités HTML pour avoir juste le premier caractères non accentués
            // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "à" => "a" ...
            $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
         
            // Remplacer les ligatures tel que : , Æ ...
            // Exemple "œ" => "oe"
            $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
            // Supprimer tout le reste
            $str = preg_replace('#&[^;]+;#', '', $str);
         
            return $str;
        }
        
        // Initialize User class
        $user = new User();
        
        // Insert or update user data to the database
        $fbUserData = array(
            'oauth_provider'=> 'facebook',
            'oauth_uid'     => $fbUserProfile['id'],
            'first_name'    => suppr_accents($fbUserProfile['first_name']),
            'last_name'     => suppr_accents($fbUserProfile['last_name']),
            'email'         => $fbUserProfile['email'],
            'gender'        => $fbUserProfile['gender'],
            'locale'        => $fbUserProfile['locale'],
            'cover'         => $fbUserProfile['cover']['source'],
            'picture'       => $fbUserProfile['picture']['url'],
            'link'          => $fbUserProfile['link']
        );
        $userData = $user->checkUser($fbUserData);
        
        // Put user data into session
        $_SESSION['userData'] = $userData;
        
        // Get logout url
        $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'/logout.php');
        
        // Render facebook profile data
        if(!empty($userData)){

            $req = $bdd->prepare('SELECT id,pseudo,pass,email,picture,niveau_privilege FROM membres WHERE oauth_uid=?');
            $req -> execute(array($userData['oauth_uid']));
            $resultat = $req->fetch();

            $_SESSION['id'] = $resultat['id'];
            $_SESSION['pseudo'] = $resultat['pseudo'];
            $_SESSION['email']=$resultat['email'];
            $_SESSION['picture'] = $resultat['picture'];
            $_SESSION['niveau_privilege'] = $resultat['niveau_privilege'];
            setcookie('pseudo',$resultat['pseudo'],time()+60*60*24*30,'/','labeautedunet.fr',true,true);
            setcookie('pass',$resultat['pass'],time()+60*60*24*30,'/','labeautedunet.fr',true,true);
        }
        else{
            $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
        }
        
    }
    else{
        // Get login url
        $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);

        // Render facebook login button
        ?><div class="img_facebook"><a class="img_facebook" href=<?php echo '"'.htmlspecialchars($loginURL).'"';?>><img class="img_facebook" src="Facebook_connexion/fblogin-btn.png"></a></div><?php
    }
    //FIN FACEBOOK CONNECT
?>