<?php
error_reporting(0);
/*
Script Name: GD Gradient Fill
Script URI: http://frenchfragfactory.net/ozh/my-projects/images-php-gd-gradient-fill/
Description: Creates a gradient fill of any shape (rectangle, ellipse, vertical, horizontal, diamond)
Author: Ozh
Version: 1.1
Author URI: http://planetozh.com/
*/

/* Release history :
* 1.1
*        - changed : more nicely packaged as a class
*        - fixed : not displaying proper gradient colors with image dimension greater than 255 (because of a limitation in imagecolorallocate)
*        - added : optional parameter 'step', more options for 'direction'
* 1.0
*        - initial release
*/

/* Usage :
*
* require_once('/path/to/gd-gradient-fill.php');
* $image = new gd_gradient_fill($width,$height,$direction,$startcolor,$endcolor,$step);
*
* Parameters :
*        - width and height : integers, dimesions of your image.
*        - direction : string, shape of the gradient.
*          Can be : vertical, horizontal, rectangle (or square), ellipse, ellipse2, circle, circle2, diamond.
*        - startcolor : string, start color in 3 or 6 digits hexadecimal.
*        - endcolor : string, end color in 3 or 6 digits hexadecimal.
*        - step : integer, optional, default to 0. Step that breaks the smooth blending effect.
* Returns a resource identifier.
*
* Examples :
*
* 1.
* require_once('/home/ozh/www/includes/gd-gradient-fill.php');
* $image = new gd_gradient_fill(200,200,'horizontal','#fff','#f00');
*
* 2.
* require_once('c:/iis/inet/include/gd-gradient-fill.php');
* $myimg = new gd_gradient_fill(80,20,'diamond','#ff0010','#303060');
*
*/


// Test it :

#$image = new gd_gradient_fill(400,200,'ellipse','#f00','#000',0);

class gd_gradient_fill {
    
    // Constructor. Creates, fills and returns an image
    function gd_gradient_fill($w,$h,$d,$s,$e,$step=0) {
        $this->width = $w;
        $this->height = $h;
        $this->direction = $d;
        $this->startcolor = $s;
        $this->endcolor = $e;
        $this->step = intval(abs($step));

        // Attempt to create a blank image in true colors, or a new palette based image if this fails
        if (function_exists('imagecreatetruecolor')) {
            $this->image = imagecreatetruecolor($this->width,$this->height);
        } elseif (function_exists('imagecreate')) {
            $this->image = imagecreate($this->width,$this->height);
        } else {
            die('Unable to create an image');
        }
        
        // Fill it
        $this->fill($this->image,$this->direction,$this->startcolor,$this->endcolor);
        
        // Show it        
        #$this->display($this->image);
        
        // Return it
        return $this->image;
    }
    
    
    // Displays the image with a portable function that works with any file type
    // depending on your server software configuration
    function display ($im) {
        if (function_exists("imagepng")) {
            header("Content-type: image/png");
            imagepng($im);
        }
        elseif (function_exists("imagegif")) {
            header("Content-type: image/gif");
            imagegif($im);
        }
        elseif (function_exists("imagejpeg")) {
            header("Content-type: image/jpeg");
            imagejpeg($im, "", 0.5);
        }
        elseif (function_exists("imagewbmp")) {
            header("Content-type: image/vnd.wap.wbmp");
            imagewbmp($im);
        } else {
            die("Doh ! No graphical functions on this server ?");
        }
        return true;
    }
    
    
    // The main function that draws the gradient
    function fill($im,$direction,$start,$end) {
        
        switch($direction) {
            case 'horizontal':
                $line_numbers = imagesx($im);
                $line_width = imagesy($im);
                list($r1,$g1,$b1) = $this->hex2rgb($start);
                list($r2,$g2,$b2) = $this->hex2rgb($end);
                break;
            case 'vertical':
                $line_numbers = imagesy($im);
                $line_width = imagesx($im);
                list($r1,$g1,$b1) = $this->hex2rgb($start);
                list($r2,$g2,$b2) = $this->hex2rgb($end);
                break;
            case 'ellipse':
                $width = imagesx($im);
                $height = imagesy($im);
                $rh=$height>$width?1:$width/$height;
                $rw=$width>$height?1:$height/$width;
                $line_numbers = min($width,$height);
                $center_x = $width/2;
                $center_y = $height/2;
                list($r1,$g1,$b1) = $this->hex2rgb($end);
                list($r2,$g2,$b2) = $this->hex2rgb($start);
                imagefill($im, 0, 0, imagecolorallocate( $im, $r1, $g1, $b1 ));
                break;
            case 'ellipse2':
                $width = imagesx($im);
                $height = imagesy($im);
                $rh=$height>$width?1:$width/$height;
                $rw=$width>$height?1:$height/$width;
                $line_numbers = sqrt(pow($width,2)+pow($height,2));
                $center_x = $width/2;
                $center_y = $height/2;
                list($r1,$g1,$b1) = $this->hex2rgb($end);
                list($r2,$g2,$b2) = $this->hex2rgb($start);
                break;
            case 'circle':
                $width = imagesx($im);
                $height = imagesy($im);
                $line_numbers = sqrt(pow($width,2)+pow($height,2));
                $center_x = $width/2;
                $center_y = $height/2;
                $rh = $rw = 1;
                list($r1,$g1,$b1) = $this->hex2rgb($end);
                list($r2,$g2,$b2) = $this->hex2rgb($start);
                break;
            case 'circle2':
                $width = imagesx($im);
                $height = imagesy($im);
                $line_numbers = min($width,$height);
                $center_x = $width/2;
                $center_y = $height/2;
                $rh = $rw = 1;
                list($r1,$g1,$b1) = $this->hex2rgb($end);
                list($r2,$g2,$b2) = $this->hex2rgb($start);
                imagefill($im, 0, 0, imagecolorallocate( $im, $r1, $g1, $b1 ));
                break;
            case 'square':
            case 'rectangle':
                $width = imagesx($im);
                $height = imagesy($im);
                $line_numbers = max($width,$height)/2;
                list($r1,$g1,$b1) = $this->hex2rgb($end);
                list($r2,$g2,$b2) = $this->hex2rgb($start);
                break;
            case 'diamond':
                list($r1,$g1,$b1) = $this->hex2rgb($end);
                list($r2,$g2,$b2) = $this->hex2rgb($start);
                $width = imagesx($im);
                $height = imagesy($im);
                $rh=$height>$width?1:$width/$height;
                $rw=$width>$height?1:$height/$width;
                $line_numbers = min($width,$height);
                break;
            default:
        }
        
        for ( $i = 0; $i < $line_numbers; $i=$i+1+$this->step ) {
            // old values :
            $old_r=$r;
            $old_g=$g;
            $old_b=$b;
            // new values :
            $r = ( $r2 - $r1 != 0 ) ? intval( $r1 + ( $r2 - $r1 ) * ( $i / $line_numbers ) ): $r1;
            $g = ( $g2 - $g1 != 0 ) ? intval( $g1 + ( $g2 - $g1 ) * ( $i / $line_numbers ) ): $g1;
            $b = ( $b2 - $b1 != 0 ) ? intval( $b1 + ( $b2 - $b1 ) * ( $i / $line_numbers ) ): $b1;
            // if new values are really new ones, allocate a new color, otherwise reuse previous color.
            // There's a "feature" in imagecolorallocate that makes this function
            // always returns '-1' after 255 colors have been allocated in an image that was created with
            // imagecreate (everything works fine with imagecreatetruecolor)
            if ( "$old_r,$old_g,$old_b" != "$r,$g,$b") 
                $fill = imagecolorallocate( $im, $r, $g, $b );
            switch($direction) {
                case 'vertical':
                    imagefilledrectangle($im, 0, $i, $line_width, $i+$this->step, $fill);
                    break;
                case 'horizontal':
                    imagefilledrectangle( $im, $i, 0, $i+$this->step, $line_width, $fill );
                    break;
                case 'ellipse':
                case 'ellipse2':
                case 'circle':
                case 'circle2':
                    imagefilledellipse ($im,$center_x, $center_y, ($line_numbers-$i)*$rh, ($line_numbers-$i)*$rw,$fill);
                    break;
                case 'square':
                case 'rectangle':
                    imagefilledrectangle ($im,$i*$width/$height,$i*$height/$width,$width-($i*$width/$height), $height-($i*$height/$width),$fill);
                    break;
                case 'diamond':
                    imagefilledpolygon($im, array (
                        $width/2, $i*$rw-0.5*$height,
                        $i*$rh-0.5*$width, $height/2,
                        $width/2,1.5*$height-$i*$rw,
                        1.5*$width-$i*$rh, $height/2 ), 4, $fill);
                    break;
                default:    
            }        
        }
    }
    
    // #ff00ff -> array(255,0,255) or #f0f -> array(255,0,255)
    function hex2rgb($color) {
        $color = str_replace('#','',$color);
        $s = strlen($color) / 3;
        $rgb[]=hexdec(str_repeat(substr($color,0,$s),2/$s));
        $rgb[]=hexdec(str_repeat(substr($color,$s,$s),2/$s));
        $rgb[]=hexdec(str_repeat(substr($color,2*$s,$s),2/$s));
        return $rgb;
    }
}
?>
