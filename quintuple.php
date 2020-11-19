<?php 

require('NFA.php');

//session
session_start();
$nfa = $_SESSION['nfa'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quintuple: NFA</title>
    <link rel="stylesheet" href="styles/quintuple.css">
</head>
<body>

    <div class="container">

        <h2>Quintuple : </h2>
        <table >
            <tr>
                <td>States</td>
                <td>&Sigma;</td>
                <td>initial State</td>
                <td>Final State</td>
                <td>Transition Functions</td>
            </tr>
            <tr>
                <td><?php echo "{ "; foreach($nfa->allState as $state) echo "Q".$state . ", "; echo "}" ?></td>
                <td><?php echo "{ "; foreach($nfa->sigma as $sigma) echo $sigma . ", "; echo "}" ?></td>
                <td><?php echo "Q" . $nfa->initialState ?></td>
                <td><?php echo "{ "; foreach($nfa->finalState as $finalState) echo "Q" . $finalState . ", "; echo "}" ?></td>
                <td><a href="#transition">Transition Functions</a></td>
            </tr>
        </table>

        <h2>Transition Functions : </h2>
        <table id="transitions">
            <tr>
                <td></td>
                <?php foreach( $nfa->sigma as $symbol ) : ?>
                    <td><?php echo $symbol ?></td>
                <?php endforeach; ?>
                <td>&Sigma;</td>
            </tr>
            <?php foreach( $nfa->transitions as $state => $transition) : ?>
                <tr>
                    <td><?php echo "Q".$state; ?></td>
                    <?php 
                    $sigma = $nfa->sigma; 
                    $sigma[] = "sigma";
                    foreach( $sigma as $symbol) : ?>
                        <?php if( isset($transition[$symbol]) ) : ?>
                            <td><?php echo "{ "; foreach( $transition[$symbol] as $destination ) echo "Q" . $destination . ", "; echo "}"  ?></td>
                        <?php else :  ?>
                            <td>&empty;</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
  
    
</body>
</html>