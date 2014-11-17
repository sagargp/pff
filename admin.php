<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "elapandya@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "1cd9fb" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'5A3D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGUMdkMQCGhhDWBsdHQJQxFhbGRoCHUSQxAIDRBodgOpEkNwXNm3ayqypK7OmIbuvFUUdVEwUaCeqeQEgdWhiIlNEGl3R3MIKtNcRzc0DFX5UhFjcBwAHXM0H1g8A2gAAAABJRU5ErkJggg==',
			'C971' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA1qRxURaWYH8gKnIYgGNIo0ODQGhKGINQLFGB5hesJOiVi1dmrV01VJk9wU0MAY6TGFoRdXL0OgQgCbWyNLo6MCA4RbWBlQxsJsbGEIDBkH4URFicR8AcznM/nGmxeQAAAAASUVORK5CYII=',
			'1E18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMEx1QBJjdRBpYAhhCAhAEhMFijGGMIJkkPQCeVPg6sBOWpk1NWzVtFVTs5Dch6YOSQybeXjtgLglRDSUMdQBxc0DFX5UhFjcBwBlash7kHIMgQAAAABJRU5ErkJggg==',
			'D59F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGUNDkMQCpog0MDo6OiCrC2gVaWBtCEQXC0ESAzspaunUpSszI0OzkNwX0MrQ6BCCrhcohmleoyO62BTWVnS3hAYwhgDdjCI2UOFHRYjFfQBUE8toA8sbFAAAAABJRU5ErkJggg==',
			'858E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMDkMREpog0MDo6OiCrC2gVaWBtCEQRA6oLQVIHdtLSqKlLV4WuDM1Ccp/IFIZGRwzzGBpd0cwD2oEhJjKFtRXdLawBjCHobh6o8KMixOI+AI0hyigRhWwuAAAAAElFTkSuQmCC',
			'236E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUMDkMREpoi0Mjo6OiCrC2hlaHRtQBVjaGVoZW1ghIlB3DRtVdjSqStDs5DdFwBUh2YeUBfQvEAUMdYGTDGRBky3hIZiunmgwo+KEIv7AHyuyUS9G6UVAAAAAElFTkSuQmCC',
			'1382' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGaY6IImxOoi0Mjo6BAQgiYk6MDS6NgQ6iKDoZQCpaxBBct/KrFVhq0JXrYpCch9UXaMDql6geQGtDJhiU1DFIG5BFhMNAbmZMTRkEIQfFSEW9wEAWY/JKEF7DFMAAAAASUVORK5CYII=',
			'37A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANEQx2mMLQiiwVMYWh0CGWYiqKylaHR0REoiiwG1MfaEADTC3bSyqhV05auilqK4r4pDAFI6qDmMTqwhqKLsTagqwuYIoIhJhoAFgsNGAThR0WIxX0AmoPMqUmPNUUAAAAASUVORK5CYII=',
			'8EEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaYGIImJTBFpYG1gCBBBEgtoBYkxOrBgqGN0QHbf0qipYUtDV2Yhuw9NHYp52MQw7UB1CzY3D1T4URFicR8Az8TKS1MrJUcAAAAASUVORK5CYII=',
			'4D2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMIQyhgYgi4WItDI6Ojogq2MMEWl0bQhEEWOdItLogBADO2natGkrs1ZmhmYhuS8ApK6VEUVvaChQbAqqGANIXQCGGFAnuphoCGtoIKqbByr8qAexuA8Akm3KIT9RsJYAAAAASUVORK5CYII=',
			'CCE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDHVqRxURaWRtdGximOiCJBTSKNADFAgKQxRpEGlgbGB1EkNwXtWraqqWhK7OmIbkPTR1uMSx2YHMLNjcPVPhREWJxHwCmcMyON/TJEAAAAABJRU5ErkJggg==',
			'48B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpI37pjCGsIYyTHVAFgthbWVtdAgIQBJjDBFpdG0IdBBBEmOdgqIO7KRp01aGLQ1dNTULyX0BUzDNCw3FNI9hCjYxTL1Y3TxQ4Uc9iMV9AALmzP74acqDAAAAAElFTkSuQmCC',
			'25EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHUNDkMREpog0sDYwOiCrC2jFFGNoFQlBEoO4adrUpUtDV4ZmIbsvgKHRFU0vkIchxtoggiEGtLUV3d7QUMYQoJtR3TJA4UdFiMV9AMHUyIt41TzMAAAAAElFTkSuQmCC',
			'E660' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGVqRxQIaWFsZHR2mOqCIiTSyNjgEBKCKNbA2MDqIILkvNGpa2NKpK7OmIbkvoEG0ldXREaYObp5rQyAWsQA0OzDdgs3NAxV+VIRY3AcAJnLNEd3GffUAAAAASUVORK5CYII=',
			'9D95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMDkMREpoi0Mjo6OiCrC2gVaXRtCMQm5uqA5L5pU6etzMyMjIpCch+rq0ijQ0hAgwiyzUC9Dg2oYgJAMUegHSIYbnEIQHYfxM0MUx0GQfhREWJxHwDaQMwGnQ4VGwAAAABJRU5ErkJggg==',
			'2CC9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxlCHaY6IImJTGFtdHQICAhAEgtoFWlwbRB0EEHWDRRjbWCEiUHcNG3aqqWrVkWFIbsvAKSOYSqyXpAuVpBdyG5pANkhgGIHUBWGW0JDMd08UOFHRYjFfQDKvsvlIdhD7AAAAABJRU5ErkJggg==',
			'207A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AMAwE48IbZCCzwRfJEkzhFNkgZAMKmJIICckBShD4K7/O0sluvYy6P+UVP4YDR2Tb+ULBKSYxHTI3BoC9zj5JGsRbv1qXcV7Gav3QuEIHt6dtSUAxWBflTNJzXimw9l2MzfnUffW/B3PjtwGtmsqRYJvtmgAAAABJRU5ErkJggg==',
			'2CA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEx1QBITmcLa6BDKEBCAJBbQKtLg6OjoIIKsGyjG2hAIE4O4adq0VUtXRUWFIbsvAKQuYCqyXkagLtbQgAZkMVYgz7UhAMUOoKpGoBiKW0JDGUNB5iG7eaDCj4oQi/sAoVLMzmUb1k8AAAAASUVORK5CYII=',
			'CDB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGaY6IImJtIq0sjY6BAQgiQU0ijS6NgQ6CCCLNQDFGh0dkN0XtWraytTQlalZSO6DqkM1rwFinggWO0QIuAWbmwcq/KgIsbgPAFSXzfVXDEQ3AAAAAElFTkSuQmCC',
			'A642' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM2QsQ2AQAhFsbgNcB+usMdELG4DnUIKNlA3sNAp1Y6Llpocv+KFwAtwPGqCkvKLX0VVB0oLORY4GBgxO4YzKiyR0DG2q2tpQueXtrXfh/FIzo+ttqCk/oYIaiNskO+7p+acXS5KnLPbOUpXwP8+zIvfCcbBzcNZh63mAAAAAElFTkSuQmCC',
			'4E09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37poiGMkxhmOqALBYi0sAQyhAQgCTGCBRjdHR0EEESY50i0sDaEAgTAztp2rSpYUtXRUWFIbkvAKwuYCqy3tBQsFiDCIpbQHY4OKCLobsFq5sHKvyoB7G4DwDAv8s18Iw8+wAAAABJRU5ErkJggg==',
			'DD34' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGRoCkMQCpoi0sjY6NKKItYo0OgBJDLFGhykBSO6LWjptZdbUVVFRSO6DqHN0wDQvMDQE0w5sbkERw+bmgQo/KkIs7gMAySnRfTZZ3VIAAAAASUVORK5CYII=',
			'CFD4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WENEQ11DGRoCkMREWkUaWBsdGpHFAhqBYg0BrShiDWCxKQFI7otaNTVs6aqoqCgk90HUBTpg6g0MDcG0A5tbUMRYQ4BiaG4eqPCjIsTiPgAGo89FOxnmRwAAAABJRU5ErkJggg==',
			'51D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGVqRxQIaGANYGx2mooqxBrA2BIQiiwUGMIDEYHrBTgqbtipqKQghu68VRR1OsQAsYiJTGEBuQREDuiQU6ObQgEEQflSEWNwHALM9yu9WVqQLAAAAAElFTkSuQmCC',
			'5007' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIaGIIkFNDCGMIQyNIigiLG2Mjo6oIgFBog0ugJlApDcFzZt2srUVVErs5Dd1wpW14piM0RsCrJYQCvYjgBkMZEpILcwOiCLsQaA3YwiNlDhR0WIxX0AvBTLd3UcdYYAAAAASUVORK5CYII=',
			'7F9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGaYGIIu2ijQwOjoEiKCJsTYEOrAgi02BiKG4L2pq2MrMyCxk9zE6AE0KgasDQ9YGkOmoYiJAyIhmR0ADpltAYgzobh6g8KMixOI+AEv+ysF1bFFUAAAAAElFTkSuQmCC',
			'DCBD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGUMdkMQCprA2ujY6OgQgi7WKNLg2BDqIoImxAtWJILkvaum0VUtDV2ZNQ3IfmjqEGBbzMOzA4hZsbh6o8KMixOI+AMykzkaxQZxSAAAAAElFTkSuQmCC',
			'10E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHaY6IImxOjCGsDYwBAQgiYk6sLayAlWLoOgVaXQF0iJI7luZNW1lauiqVVFI7oOqa3TA1NuK6haQHQxTUMUgbkEWEw0BudkxNGQQhB8VIRb3AQCHmch0iZJ+LgAAAABJRU5ErkJggg==',
			'D038' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhDGaY6IIkFTGEMYW10CAhAFmtlbWVoCHQQQRETaXRAqAM7KWrptJVZU1dNzUJyH5o6hBiGeVjswOIWbG4eqPCjIsTiPgA3EM6cpu67twAAAABJRU5ErkJggg==',
			'B5BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGUMDkMQCpog0sDY6OiCrC2gFijUEoopNEQlBUgd2UmjU1KVLQ1eGZiG5L2AKQ6MrhnlAMXTzWkUwxaawtqK7JTSAMQTdzQMVflSEWNwHAII8zH2dlyTaAAAAAElFTkSuQmCC',
			'5CD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDGUMDkMQCGlgbXRsdHRhQxEQaXBsCUcQCA0QaWBsCXR2Q3Bc2bdqqpasio6KQ3dcKUgcyAUk3FrGAVogdyGIiU0BucQhAdh9rAMjNDFMdBkH4URFicR8AdTDNPrNKfWQAAAAASUVORK5CYII=',
			'1DB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGRoCkMRYHURaWRsdGpHFRB1EGl0bAloDUPQCxRodpgQguW9l1rSVqaGroqKQ3AdR5+iAobchMDQEQyygAU0dyC0oYqIhmG4eqPCjIsTiPgDA+8y7+ttPTQAAAABJRU5ErkJggg==',
			'042A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCgRhJjDWAYSqjo8NUByQxkSkMoawNAQEBSGIBrYyuDA2BDiJI7otaunTpqpWZWdOQ3BfQKtLK0MoIUwcVEw11mMIYGoJqRytDAKo6oFuAOlHFQG5mDQ1EERuo8KMixOI+AJpUye9quIDOAAAAAElFTkSuQmCC',
			'4D81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpI37poiGMIQytKKIhYi0Mjo6TEUWYwwRaXRtCAhFFmOdItLo6OgA0wt20rRp01Zmha5aiuy+AFR1YBgaCjYP1d4pWMVAbkETA7s5NGAwhB/1IBb3AQB0U8yZqFYGGgAAAABJRU5ErkJggg==',
			'0533' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQxlDGUIdkMRYA0QaWBsdHQKQxESmiADJgAYRJLGAVpEQhkaHhgAk90Utnbp01dRVS7OQ3BfQClSFUIcQQzMPaAeGGGsAayu6WxgdGEPQ3TxQ4UdFiMV9ALSQzXkyPf5pAAAAAElFTkSuQmCC',
			'266C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaYGIImJTGFtZXR0CBBBEgtoFWlkbXB0YEHW3SrSwNrA6IDivmnTwpZOXZmF4r4A0VZWR0cHZHsZHUQaXRsCUcRYGyBiyHYAbcBwS2goppsHKvyoCLG4DwC2mcpLwzzZlwAAAABJRU5ErkJggg==',
			'281C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDCECCCJBbQKtLoGMLowIKsuxWobgqjA4r7pq0MA+IsFPcFoKgDQ0YHkUYHNDHWBogYsh0iDSC9qG4JDWUMYQx1QHHzQIUfFSEW9wEAdB3KGVNDTm4AAAAASUVORK5CYII=',
			'B8FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA0MdkMQCprC2sjYwOgQgi7WKNLoCxURwqwM7KTRqZdjS0JWhWUjuI9o8wnYg3NzAiOLmgQo/KkIs7gMAVEzMRhffIbYAAAAASUVORK5CYII=',
			'0711' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMLQii7EGMDQ6hDBMRRYTmcLQ6BjCEIosFtAK1IfQC3ZS1NJV04BwKbL7gOoCGNDsCGhldEAXE5nC2oAuxhoggiHG6CDSwBjqEBowCMKPihCL+wDFXMsROV22xgAAAABJRU5ErkJggg==',
			'3E18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANEQxmmMEx1QBILmCLSwBDCEBCArLJVpIExhNFBBFkMpG4KXB3YSSujpoatmrZqahay+1DVwc1jmIJmHhaxACx6QW5mDHVAcfNAhR8VIRb3AQDRMMsrKin+kQAAAABJRU5ErkJggg==',
			'B3E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDHaY6IIkFTBFpZW1gCAhAFmtlaHRtYHQQQVHHAFLXIILkvtCoVWFLQ1etikJyH1RdowOGeQytDJhiUxiwuAXTzY6hIYMg/KgIsbgPAIWJzTXEPvEgAAAAAElFTkSuQmCC',
			'66F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZW1gCAhAEgtoEWlkbWB0EEEWaxBpYAWpR3JfZNS0sKWhq1ZFIbkvZIooyLxGZDsCWkUaXRsYWhkwxaYwYHELhpsbGENDBkH4URFicR8A6DrL8fMMb5oAAAAASUVORK5CYII=',
			'6E7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MDkMREpogAyUAHZHUBLVjEGoBijY4wMbCTIqOmhq1aujI0C8l9ISDzpjCi6m0FigVgijE6oIqB3MLagCoGdnMDI4qbByr8qAixuA8AujzJ/ajtYc8AAAAASUVORK5CYII=',
			'5AAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIYGIIkFNDCGMIQyOjCgiLG2Mjo6oogFBog0ujYEwsTATgqbNm1l6qrI0Cxk97WiqIOKiYa6hqKKBWBRJzIFU4wVYi+Kmwcq/KgIsbgPAB6Jy7Ye2as1AAAAAElFTkSuQmCC',
			'6741' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQx0aHVqRxUSmMABFHKYiiwW0AMWmOoSiiDUwtDIEwvWCnRQZtWraysyspcjuC5nCEMCKZkdAK6MDa2gAmhhrAwOGW0QwxFgDwGKhAYMg/KgIsbgPANc4zXRyCTeSAAAAAElFTkSuQmCC',
			'54B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYWllDGVqRxQIaGKayNjpMdUAVC2VtCAgIQBILDGB0ZW10dBBBcl/YtKVLl4auzJqG7L5WkVYkdVAx0VDXhkAUsYBWoFvQ7BCZAhRDcwtrAKabByr8qAixuA8A403Mr5gtJ2kAAAAASUVORK5CYII=',
			'4092' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpI37pjAEMIQyTHVAFgthDGF0dAgIQBJjDGFtZW0IdBBBEmOdItLo2hDQIILkvmnTpq3MzIxaFYXkvgCgOoeQgEZkO0JDgWINAa2obmFtZQSqRhWDuAXTzYyhIYMh/KgHsbgPAH22y7JgJHMiAAAAAElFTkSuQmCC',
			'DA75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0MDkMQCpjCGMDQEOiCrC2hlbcUUE2l0aHR0dUByX9TSaSuzlq6MikJyH1jdFIYGERS9oqEOAehiIo2ODowOKGJTRBpdGxgCkN0XGgAWm+owCMKPihCL+wBAkc4Mqsd6dwAAAABJRU5ErkJggg==',
			'F55E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHUMDkMQCGkQaWBsYHRgIi4WwToWLgZ0UGjV16dLMzNAsJPcBzW50aAhE04tNTKTRFUOMtZXR0RFNjDGEIZQRxc0DFX5UhFjcBwAUpcthU4iLuQAAAABJRU5ErkJggg==',
			'0E97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUNDkMRYA0QaGB0dGkSQxESmiDSwNgSgiAW0QsQCkNwXtXRq2MrMqJVZSO4DqWMICWhlQNMLJKcwoNnB2BAQwIDhFkcHLG5GERuo8KMixOI+AKGHyrnwM+GWAAAAAElFTkSuQmCC',
			'B43A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGVqRxQKmMExlbXSY6oAs1soQCiQDAlDUMboyNDo6iCC5LzRq6dJVU1dmTUNyX8AUkVYkdVDzREMdGgJDQ1DtALojEFXdFIZWVjS9EDczoogNVPhREWJxHwD/qc2Dyyt3oAAAAABJRU5ErkJggg==',
			'012E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMDkMRYAxgDGB0dHZDViUxhDWBtCEQRC2gF6kWIgZ0UtXRV1KqVmaFZSO4Dq2tlxNQ7hRHNDqBYAKoYK1gEVYzRgTWUNTQQxc0DFX5UhFjcBwAv2caHoVew7QAAAABJRU5ErkJggg==',
			'27D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DGUIdkMREpjA0ujY6OgQgiQW0AsUaAhpEkHW3MrSyAsUCkN03bdW0pauilmYhuy+AIQBJHRgyOjA6sKKZxwqGqGIiQMiK5pbQUKAYmpsHKvyoCLG4DwCVbM0uP1naMgAAAABJRU5ErkJggg==',
			'3372' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANYQ1hDA6Y6IIkFTBFpBZIBAcgqWxkaHRoCHUSQxaZAREWQ3LcyalXYqqWrVkUhuw+kbgpIJZp5AUASTczRAagSzS2sDQwBGG5uYAwNGQThR0WIxX0Ai97MMSKYHXwAAAAASUVORK5CYII=',
			'CA90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGVqRxURaGUMYHR2mOiCJBTSytrI2BAQEIIs1iDS6NgQ6iCC5L2rVtJWZmZFZ05DcB1LnEAJXBxUTDXVoQBNrFGl0RLNDpBUohuYW1hCgeWhuHqjwoyLE4j4AIc/NQcIVTeUAAAAASUVORK5CYII=',
			'98F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0NDkMREprC2soJoJLGAVpFGVwwxiLoAJPdNm7oybGnoqpVZSO5jdQWra0WxGWLeFGQxAYhYALIYxC2MDhhuRhMbqPCjIsTiPgD998rkUcmplgAAAABJRU5ErkJggg==',
			'9D6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gVaXRtcAgIwBBjdBBBct+0qdNWpk5dmTUNyX2srkB1jo4wdRAI1hsYGoIkJgARQ1EHcQuqXoibGVHNG6DwoyLE4j4AO2zMAvEYaPQAAAAASUVORK5CYII=',
			'B155' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUMDkMQCpjAGsDYwOiCrC2hlxRSbAtQ7ldHVAcl9oVGropZmZkZFIbkPpA5INoigmIddjLUh0EEEzQ5GR4cAZPeFAl3MEMow1WEQhB8VIRb3AQDrPMqM+TDFVQAAAABJRU5ErkJggg==',
			'5BBA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGVqRxQIaRFpZGx2mOqCKNbo2BAQEIIkFBoDUOTqIILkvbNrUsKWhK7OmIbuvFUUdTAxoXmBoCLIdEDEUdSJTMPWyBoDczIhq3gCFHxUhFvcBAFf6zNj4oA9RAAAAAElFTkSuQmCC',
			'69C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHUMDkMREprC2MjoEOiCrC2gRaXRtEEQVawCJMbo6ILkvMmrp0tRVK6OikNwXMoUx0BVkLrLeVoZGTDEWsB3IYhC3BAQguw/iZoepDoMg/KgIsbgPAMyRzAmod8eWAAAAAElFTkSuQmCC',
			'2D62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bXB0EEHWDRYDqkd237RpK1OnrloVhey+AKA6R4dGZDsYHUB6A1pR3NIAFpuCLCbSAHELslhoKMjNjKEhgyD8qAixuA8AylDMpJB3Pc4AAAAASUVORK5CYII=',
			'4EE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpI37poiGsoY6tKKIhYg0sDYwTHVAEmOEiAUEIImxTgGJMTqIILlv2rSpYUtDV2ZNQ3JfAKo6MAwNxRRjmIJpB1QMxS1Y3TxQ4Uc9iMV9AFKLys+jZGHxAAAAAElFTkSuQmCC',
			'A6F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDAxoCkMRYA1hbWRsYGpHFRKaINALFWpHFAlpFGoBiUwKQ3Be1dFrY0tBVUVFI7gtoFQWax+iArDc0VKTRtYExNATVPKAYQwOqHWC3oIkB3YwmNlDhR0WIxX0AlRjNoQLhzJMAAAAASUVORK5CYII=',
			'E63C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGaYGIIkFNLC2sjY6BIigiIk0MjQEOrCgijUwNDo6ILsvNGpa2KqpK7OQ3RfQINqKpA5ungPQPGxiqHZgugWbmwcq/KgIsbgPADt9zRitWY24AAAAAElFTkSuQmCC',
			'1D81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgdRFoZHR2mIouJOog0ujYEhKLqFWl0dHSA6QU7aWXWtJVZoauWIrsPTR1cDGgeMWIgt6CIiYaA3RwaMAjCj4oQi/sAK9PJ6ThIc4IAAAAASUVORK5CYII=',
			'704C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHaYGIIu2MoYwtDoEiKCIsbYyTHV0YEEWmyLS6BDo6IDivqhpKzMzM7OQ3cfoINLo2ghXB4asDUCx0EAUMZEGoB2NqHYENADd0ojqFqBbMd08QOFHRYjFfQAnZct6Xwno/QAAAABJRU5ErkJggg==',
			'05C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHaY6IImxBogAxQMCApDERKaINLA2CDqIIIkFtIqEsDYwwNSBnRS1dOrSpatWTc1Ccl9AK0OjK0IdkhgjinlAO4BiqHawBrC2oruF0YExBN3NAxV+VIRY3AcAsqvL1ep9hREAAAAASUVORK5CYII=',
			'907E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0MDkMREpjCGMDQEOiCrC2hlbcUUE2l0aHSEiYGdNG3qtJVZS1eGZiG5j9UVqG4KI4peBpDeAFQxAaAdjA6oYiC3sDagioHd3MCI4uaBCj8qQizuAwAPsMleLAhOUwAAAABJRU5ErkJggg==',
			'4B04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGMExhaAhAFgsRaWUIZWhEFmMMEWl0dHRoRRZjnSLSytoQMCUAyX3Tpk0NW7oqKioKyX0BYHWBDsh6Q0NFGl0bAkNDUNwCtgPVLVPAbkETw+LmgQo/6kEs7gMAKmrN7q4PueIAAAAASUVORK5CYII=',
			'312C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGaYGIIkFTGEMYHR0CBBBVtnKGsDaEOjAgiw2BagXKIbsvpVRq6JWrczMQnEfSF0rowOKza1AsSlYxAIYUewImAISYUBxi2gAayhraACKmwcq/KgIsbgPAASRx/oVnbC/AAAAAElFTkSuQmCC',
			'CFB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WENEQ11DGaY6IImJtIo0sDY6BAQgiQU0AsUaAh1EkMUawOoaRJDcF7VqatjSUBCNcB9UXaMDut6GgFYGDDsCpjBgcQuqm4FioYyhIYMg/KgIsbgPAA9RzZ8DhxZxAAAAAElFTkSuQmCC',
			'9A17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYQgB0khiAa2srYwYYiKNDlOANJL7pk2dtjJr2qqVWUjuY3UFq2tFsblVNBQoNgVZTABiXgADiltAYowOqG4WaXQMdUQRG6jwoyLE4j4AkjDLwCFteQYAAAAASUVORK5CYII=',
			'D513' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIQ6IIkFTBFpYAhhdAhAFmsVaWAMYWgQQRULAeptCEByX9TSqUtXTVu1NAvJfQGtDI0OCHUoYmjmYYpNYW1lmILqltAAxhDGUAcUNw9U+FERYnEfAKa5zkcM9o49AAAAAElFTkSuQmCC',
			'F500' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMLQiiwU0iDQwhDJMdUATY3R0CAhAFQthbQh0EEFyX2jU1KVLV0VmTUNyH1BPoytCHR4xkUZHDDtYWzHdwhiC7uaBCj8qQizuAwCyF81+W7pYiwAAAABJRU5ErkJggg==',
			'2387' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUNDkMREpoi0Mjo6NIggiQW0MjS6NgSgiDG0MoDVBSC7b9qqsFWhq1ZmIbsvAKyuFdleRgeweVNQ3NIAFgtAFhNpALnF0QFZLDQU7GYUsYEKPypCLO4DAMyDyuGpceAIAAAAAElFTkSuQmCC',
			'A358' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDHaY6IImxBoi0sjYwBAQgiYlMYWh0BaoWQRILaGVoZZ0KVwd2UtTSVWFLM7OmZiG5D6QOSKKYFxrK0OjQEIhuHtAOdDGRVkZHBxS9Aa2sIQyhDChuHqjwoyLE4j4AdjPMtmCt+3MAAAAASUVORK5CYII=',
			'B4A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYWhmmADGSWMAUhqkMoQxTHZDFWhlCGR0dAgJQ1DG6sjYEOogguS80aunSpasis6YhuS9gikgrkjqoeaKhrqHoYgxAdQFodoDFUNwCcjMrSPUgCD8qQizuAwAXbc39tSll1gAAAABJRU5ErkJggg==',
			'84C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHUIdkMREpjBMZXQIdAhAEgsAqmJtEGgQQVHH6MoKkkNy39KopUuXAqksJPeJTBFpRVIHNU801BUkh2pHK6YdDK3obsHm5oEKPypCLO4DAKCRzJloCVbXAAAAAElFTkSuQmCC',
			'3AD7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGUNDkMQCpjCGsDY6NIggq2xlbWVtCEAVmyLS6AoUC0By38qoaStTV0WtzEJ2H0RdK4rNraKhQLEpqGJgdQEMKG4BijU6OqC6GSgWyogiNlDhR0WIxX0AfSDNSpEdBMkAAAAASUVORK5CYII=',
			'48B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpI37pjCGsIYytKKIhbC2sjY6TEUWYwwRaXRtCAhFFmOdAlYH0wt20rRpK8OWhq5aiuy+AFR1YBgaCjYP1d4p2MQw9ULdHBowGMKPehCL+wC46MzSDz7m6QAAAABJRU5ErkJggg==',
			'D003' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIQ6IIkFTGEMYQhldAhAFmtlbWV0dGgQQRETaXRtCGgIQHJf1NJpK1OBZBaS+9DUoYiJELIDi1uwuXmgwo+KEIv7AIswzhbz3QHhAAAAAElFTkSuQmCC',
			'2662' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWlkbXB0EEHW3SrSwApSj+y+adPClk5dtSoK2X0Boq2sjg6NyHYwOog0ugJNRXFLA1hsCrIY0AawW5DFQkNBbmYMDRkE4UdFiMV9AFyZy48bXzPSAAAAAElFTkSuQmCC',
			'5E01' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMLQiiwU0iDQwhDJMRRdjdHQIRRYLDBBpYG0IgOkFOyls2tSwpauilqK4rxVFHU6xgFawHShiIlPAbkERYw0Auzk0YBCEHxUhFvcBAL3Dy8Jf7nhaAAAAAElFTkSuQmCC',
			'3646' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxgaHaY6IIkFTGFtZWh1CAhAVtkq0sgw1dFBAFlsikgDQ6CjA7L7VkZNC1uZmZmahey+KaKtrI2OGOa5hgY6iKCJOTQ6ooiB3dKI6hZsbh6o8KMixOI+AEeHzF4CyS2AAAAAAElFTkSuQmCC',
			'AE2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUNDkMRYA0QaGB0dHZDViUwRaWBtCEQRC2gVAZJwMbCTopZODVu1MjM0C8l9YHWtjCh6Q0OBYlMYMc0LwBRjdEAXEw1lDUV1y0CFHxUhFvcBAEPTySMJzkShAAAAAElFTkSuQmCC',
			'36DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDGUMdkMQCprC2sjY6OgQgq2wVaWRtCHQQQRabItKAJAZ20sqoaWFLV0VmTUN23xTRVgy9QPNciRDD5hZsbh6o8KMixOI+AH7ly7Yn/NjAAAAAAElFTkSuQmCC',
			'4F5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpI37poiGuoY6hoYgi4WINLA2MDogq2PEIsY6BSg2FS4GdtK0aVPDlmZmhmYhuS8AqI6hIRBFb2gophgDyDwsYoyOjhhiDKGobhmw8KMexOI+AI4qyT6OE58AAAAAAElFTkSuQmCC',
			'2E22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QwQ2AIAxFy4ENyj64QUmsB0dwinpgA3UHmdJy+4keNbH/1Bf4vEDtNkZ/yid+UZKS0p6B8cYWhiwCTCpbtJIZb9e+iTH6HfvUzqXN6Cd+otKKb4TetDlFl94kTtHFE7JTYKpJoxYdf/B/L+bB7wKUocrEVtpslgAAAABJRU5ErkJggg==',
			'A6B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWlkbQh0EEESC2gVaQCqaxBBcl/U0mlhS0OBNJL7AlpFQeY1ItsRGirS6AqUYUA1DyQ2BVUM4hZUMZCbGUNDBkH4URFicR8AjejNl4hzWc0AAAAASUVORK5CYII=',
			'B2D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaXRtCHQQQVHHABQLgKkDOyk0atXSpauipmYhuQ+obgorQh3UPIYAVnTzWhkdMMSAOtHdEhogGuqK5uaBCj8qQizuAwBR0c6lPiVeTAAAAABJRU5ErkJggg==',
			'4908' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjCGMExhmOqALBbC2soQyhAQgCTGGCLS6Ojo6CCCJMY6RaTRtSEApg7spGnTli5NXRU1NQvJfQFTGAOR1IFhaCgDUG8ginkMU1gw7GCYgukWrG4eqPCjHsTiPgAvV8xHtJMXPgAAAABJRU5ErkJggg==',
			'73C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNZQxhCHRoCkEVbRVoZHQIaUcUYGl0bBFpRxKYwtLICyQBk90WtClsKJKOQ3MfoAFIHNBFJL1Af0DzG0BAkMRGwmACKWwIawG5BE8Pi5gEKPypCLO4DAKD3zXmmFGhdAAAAAElFTkSuQmCC',
			'0992' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWl0bQh0EEESC2gFiQU0iCC5L2rp0qWZmVGropDcF9DKGOgQEtDogKKXAcgHkih2sDQ6NgRMYcDiFkw3M4aGDILwoyLE4j4AF17MDs+K2XsAAAAASUVORK5CYII=',
			'A434' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2Quw2AMAxELwUbhH1MQW8k3GSauPAGYYQ0mRLozKcEga97OltPRrtMxp/yil8gWBBkdqxjLJ2SehYL9pZ5xhZGKBV2fqnW2paWkvNjiwYdyO+K9EJ5kvlwD7ab8IltLhd2dv7qfw/mxm8FNxHO+Ion9mkAAAAASUVORK5CYII=',
			'A85C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHaYGIImxBrC2sjYwBIggiYlMEWl0BapmQRILaAWqm8rogOy+qKUrw5ZmZmYhuw+kjqEh0AHZ3tBQkUYHNLGAVpAdgRh2MDo6oLgloJUxhCGUAcXNAxV+VIRY3AcAF+fLn5DefZUAAAAASUVORK5CYII=',
			'CCF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDA1qRxURaWRtdGximIosFNIo0AMVCUcQaRBpYGxhgesFOilo1bdXS0FVLkd2Hpg63GMQObG5BEQO7GeiWgEEQflSEWNwHAKu/zJO2JMH0AAAAAElFTkSuQmCC',
			'FCAA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMLQiiwU0sDY6hDJMdUARE2lwdHQICEATY20IdBBBcl9o1LRVS1dFZk1Dch+aOoRYaGBoCJqYK4Y61kZMMcZQdPMGKvyoCLG4DwCXps4zz1ESdQAAAABJRU5ErkJggg==',
			'3B33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RANEQxhDGUIdkMQCpoi0sjY6OgQgq2wVaXRoCGgQQRYDqmMAiyLctzJqatiqqauWZiG7D1UdbvOwiGFzCzY3D1T4URFicR8AaPPOATAfwXIAAAAASUVORK5CYII=',
			'4A23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsQ3AIAwE7YINyD6mSO8CL8EUTsEGiA1SkCkTOiNSJkr83en1OhmO6RT+lHf8CjAICFkWMWIIxIZhdNkpqzfMFb/Rxdj41VpbamlPxo97L4PaPZFFqMCwB73HMwuEg0tnq/Do/NX/nsuN3wma4szcT7lB7AAAAABJRU5ErkJggg==',
			'C1B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYAlhDGaY6IImJtDIGsDY6BAQgiQU0sgawNgQ6iCCLNQD1NjrCxMBOigKipaGrosKQ3AdR5zAVQy+QRBFrBIuh2CHSyoDhFtYQ1lB0Nw9U+FERYnEfAISGyu0ZTIndAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>