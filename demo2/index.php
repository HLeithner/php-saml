<?php
/**
 * SAMPLE Code to demonstrate how to initiate a SAML Authorization request
 *
 * When the user visits this URL, the browser will be redirected to the SSO
 * IdP with an authorization request. If successful, it will then be
 * redirected to the consume URL (specified in settings) with the auth
 * details.
 */

session_start();

require_once '../_toolkit_loader.php';

if (!isset($_SESSION['samlUserdata'])) {
    $settings = new OneLogin\Saml2\Settings();
    $authRequest = new OneLogin\Saml2\AuthnRequest($settings);
    $samlRequest = $authRequest->getRequest();

    $parameters = array('SAMLRequest' => $samlRequest);
    $parameters['RelayState'] = OneLogin\Saml2\Utils::getSelfURLNoQuery();

    $idpData = $settings->getIdPData();
    $ssoUrl = $idpData['singleSignOnService']['url'];
    $url = OneLogin\Saml2\Utils::redirect($ssoUrl, $parameters, true);

    header("Location: $url");
} else {
    if (!empty($_SESSION['samlUserdata'])) {
        $attributes = $_SESSION['samlUserdata'];
        echo 'You have the following attributes:<br>';
        echo '<table><thead><th>Name</th><th>Values</th></thead><tbody>';
        foreach ($attributes as $attributeName => $attributeValues) {
            echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
            foreach ($attributeValues as $attributeValue) {
                echo '<li>' . htmlentities($attributeValue) . '</li>';
            }
            echo '</ul></td></tr>';
        }
        echo '</tbody></table>';
        if (!empty($_SESSION['IdPSessionIndex'])) {
            echo '<p>The SessionIndex of the IdP is: '.htmlentities($_SESSION['IdPSessionIndex']).'</p>';
        }
    } else {
        echo "<p>You don't have any attribute</p>";
    }
    echo '<p><a href="slo.php">Logout</a></p>';
}
