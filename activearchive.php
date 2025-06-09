<?php
// Your LDAP connection settings
$ldapServer = '10.152.10.04'; // Replace with your LDAP server address
$ldapPort = '389'; // Replace with your LDAP server port
$ldapBaseDn = 'ou=temple,DC=tu,DC=temple,DC=edu'; // Replace with your LDAP base DN
$ldapBindDn = 'tu.temple.edu/Temple/Colleges-Campuses/Japan/Users/TUJ-Services/SVC.JapanKisok'; // Replace with your LDAP bind DN
$ldapBindPassword = ',4eb9f6V4XJj(\L*,q6~\>p9@'; // Replace with your LDAP bind password

// Function to format search results into a box
function formatResultBox($employeeID, $name, $email)
{
    echo '<div class="result-box">';
    echo "<strong>Employee ID:</strong> $employeeID<br>";
    echo "<strong>Name:</strong> $name<br>";
    echo "<strong>Email:</strong> $email<br>";
    echo '</div>';
}

// Check if TUID parameter exists in the URL
$tuid = isset($_GET['tuid']) ? $_GET['tuid'] : null;

// If TUID exists, perform LDAP search
if ($tuid !== null) {
    // Connect to LDAP server
    $ldapConn = ldap_connect($ldapServer, $ldapPort) or die("Could not connect to LDAP server.");

    // Bind to LDAP server
    ldap_bind($ldapConn, $ldapBindDn, $ldapBindPassword) or die("Could not bind to LDAP server.");

    // Search for the user with the given employeeID
    $searchFilter = "(employeeID=$tuid)";
    $searchResults = ldap_search($ldapConn, $ldapBaseDn, $searchFilter);
    $entries = ldap_get_entries($ldapConn, $searchResults);

    // If user is found, display details
    if ($entries['count'] > 0) {
        $name = $entries[0]['displayname'][0];
        $email = $entries[0]['mail'][0];
        formatResultBox($tuid, $name, $email);
    } else {
        echo "User not found.";
    }

    // Close LDAP connection
    ldap_close($ldapConn);
}
?>
