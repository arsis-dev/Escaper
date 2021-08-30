# Escaper
PHP error out redirection across pages

#### Initialize in a shared config file:
`$escaper = new Escaper();`

#### In a script where an error may occur, set the escape route:
/**
 *  @var Escaper $escaper 
 */
`$escaper->route = '../login.php';`

#### When an error occurs, set the error message and redirect away:
`$escaper->escape('Invalid email address!');`

*User is redirected to the escape route URL and the error message can then be picked up and displayed in the view.*

#### If an error was sent, find and show it:
`if ($escaper->hasError) $escaper->message();`
Outputs "Invalid email address!"

---
#### Escaper can also be used to send success messages like *Password changed successfully!*:

`$escaper->route = '../pw_change_success.php';`

`$escaper->success('Password changed successfully!');`
