<?php
// --- Set the response type to JSON ---
header('Content-Type: application/json');

// --- Initialize response array ---
$response = [
    'success' => false,
    'message' => 'An error occurred.',
    'data' => null
];

// --- Get TUID from the request ---
$tuid = $_GET['tuid'] ?? null;

// --- Validate TUID ---
if (empty($tuid) || !ctype_digit($tuid)) {
    $response['message'] = 'Invalid or missing TUID.';
    echo json_encode($response);
    exit;
}

// Your LDAP connection settings
$ldapServer = '10.152.10.04';
$ldapPort = '389';
$ldapBaseDn = 'ou=temple,DC=tu,DC=temple,DC=edu';
$ldapBindDn = 'tu.temple.edu/Temple/Colleges-Campuses/Japan/Users/TUJ-Services/SVC.JapanKisok';
$ldapBindPassword = ',4eb9f6V4XJj(\L*,q6~\>p9@';

// Connect to LDAP server
$ldapConn = ldap_connect($ldapServer, $ldapPort);

if (!$ldapConn) {
    $response['message'] = 'Could not connect to LDAP server.';
    echo json_encode($response);
    exit;
}

// Set LDAP protocol version
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Bind to LDAP server
$ldapBind = ldap_bind($ldapConn, $ldapBindDn, $ldapBindPassword);

if (!$ldapBind) {
    $response['message'] = 'Could not bind to LDAP server. Check credentials.';
    ldap_close($ldapConn);
    echo json_encode($response);
    exit;
}

// Search for the user with the given employeeID
$searchFilter = "(employeeID=$tuid)";
$searchResults = ldap_search($ldapConn, $ldapBaseDn, $searchFilter);
$entries = ldap_get_entries($ldapConn, $searchResults);

// If user is found, populate the response data
if ($entries['count'] > 0) {
    $response['success'] = true;
    $response['message'] = 'User found.';
    $response['data'] = [
        'employeeID' => $tuid,
        'name' => $entries[0]['displayname'][0] ?? 'N/A',
        'email' => $entries[0]['mail'][0] ?? 'N/A'
    ];
} else {
    $response['message'] = "User with TUID '$tuid' not found in Active Directory.";
}

// Close LDAP connection
ldap_close($ldapConn);

// --- Echo the final JSON response ---
echo json_encode($response);
?>
