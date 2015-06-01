<?php

class vcImageFilter {

	/**
	 * @var resource
	 */
	private $image;

	/**
	 * Directory for image assets.
	 * @var string
	 */
	private $assetDirectory;

	/**
	 * run constructor
	 *
	 * @param resource &$image GD image resource
	 */
	public function __construct( &$image ) {
		$this->image = $image;

		$this->assetDirectory = dirname( dirname( dirname( __FILE__ ) ) ) . '/assets/';
	}

	/**
	 * Get the current image resource
	 *
	 * @return resource
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Apply PNG overlay
	 *
	 * @param $overlay_image
	 * @param $amount
	 *
	 * @return mixed
	 */
	public function applyOverlay( $overlay_image, $amount ) {
		$w = imagesx( $this->image );
		$h = imagesy( $this->image );
		$filter = imagecreatetruecolor( $w, $h );

		imagealphablending( $filter, false );
		imagesavealpha( $filter, true );

		$transparent = imagecolorallocatealpha( $filter, 255, 255, 255, 127 );
		imagefilledrectangle( $filter, 0, 0, $w, $h, $transparent );

		$ext = strtolower( substr( $overlay_image, - 3, 3 ) );
		switch ( $ext ) {
			case 'jpg':
				$overlay = imagecreatefromjpeg( $this->assetDirectory . $overlay_image );
				break;

			case 'png':
				$overlay = imagecreatefrompng( $this->assetDirectory . $overlay_image );
				break;

			case 'gif':
				$overlay = imagecreatefromgif( $this->assetDirectory . $overlay_image );
				break;

			default:
				$overlay = false;

		}

		if ( ! $overlay ) {
			return $this;
		}

		$w2 = imagesx( $overlay );
		$h2 = imagesy( $overlay );
		imagecopyresampled( $filter, $overlay, 0, 0, 0, 0, $w, $h, $w2, $h2 );

		$comp = imagecreatetruecolor( $w, $h );
		imagecopy( $comp, $this->image, 0, 0, 0, 0, $w, $h );
		imagecopy( $comp, $filter, 0, 0, 0, 0, $w, $h );
		imagecopymerge( $this->image, $comp, 0, 0, 0, 0, $w, $h, $amount );

		imagedestroy( $comp );

		return $this;
	}

	public function bubbles() {
		$this->applyOverlay( 'pattern4.jpg', 20 );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 40 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 10 );

		return $this;
	}

	public function colorise() {
		$this->applyOverlay( 'pattern5.jpg', 40 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 25 );

		return $this;
	}

	public function sepia() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 100, 50, 0 );

		return $this;
	}

	public function sharpen() {
		$gaussian = array(
			array( 1.0, 1.0, 1.0 ),
			array( 1.0, - 7.0, 1.0 ),
			array( 1.0, 1.0, 1.0 )
		);
		imageconvolution( $this->image, $gaussian, 1, 4 );

		return $this;
	}

	public function emboss() {
		$gaussian = array(
			array( - 2.0, - 1.0, 0.0 ),
			array( - 1.0, 1.0, 1.0 ),
			array( 0.0, 1.0, 2.0 )
		);

		imageconvolution( $this->image, $gaussian, 1, 5 );

		return $this;
	}

	public function cool() {
		imagefilter( $this->image, IMG_FILTER_MEAN_REMOVAL );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 50 );

		return $this;
	}

	public function old2() {
		$this->applyOverlay( 'pattern1.jpg', 40 );

		return $this;
	}

	public function old3() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 30 );
		$this->applyOverlay( 'pattern3.jpg', 50 );

		return $this;
	}

	public function old() {
		$this->applyOverlay( 'bg1.jpg', 30 );

		return $this;
	}

	public function light() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 100, 50, 0, 10 );

		return $this;
	}

	public function aqua() {
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 70, 0, 30 );

		return $this;
	}

	public function fuzzy() {
		$gaussian = array(
			array( 1.0, 1.0, 1.0 ),
			array( 1.0, 1.0, 1.0 ),
			array( 1.0, 1.0, 1.0 )
		);

		imageconvolution( $this->image, $gaussian, 9, 20 );

		return $this;
	}

	public function boost() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 35 );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );

		return $this;
	}

	public function gray() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 60 );
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );

		return $this;
	}

	public function dreamy() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 20 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 35 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 60, - 10, 35 );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, 7 );
		$this->applyOverlay( 'scratch.png', 10 );
		$this->applyOverlay( 'vignette.png', 100 );

		return $this;
	}

	public function velvet() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 5 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 25 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, - 10, 45, 65 );
		$this->applyOverlay( 'noise.png', 45 );
		$this->applyOverlay( 'vignette.png', 100 );

		return $this;
	}

	public function chrome() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 15 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 15 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, - 5, - 10, - 15 );
		$this->applyOverlay( 'noise.png', 45 );
		$this->applyOverlay( 'vignette.png', 100 );

		return $this;
	}

	public function lift() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 50 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 25 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 75, 0, 25 );
		$this->applyOverlay( 'emulsion.png', 100 );

		return $this;
	}

	public function canvas() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 25 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 25 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 50, 25, - 35 );
		$this->applyOverlay( 'canvas.png', 100 );

		return $this;
	}

	public function vintage() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 15 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 25 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, - 10, - 5, - 15 );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, 7 );
		$this->applyOverlay( 'scratch.png', 7 );

		return $this;
	}

	public function monopin() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 15 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 15 );
		$this->applyOverlay( 'vignette.png', 100 );

		return $this;
	}

	public function antique() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 0 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 30 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 75, 50, 25 );

		return $this;
	}

	public function blackwhite() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 20 );

		return $this;
	}

	public function boost2() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 35 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 25, 25, 25 );

		return $this;
	}

	public function sepia2() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 10 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 20 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 60, 30, - 15 );

		return $this;
	}

	public function blur() {
		imagefilter( $this->image, IMG_FILTER_SELECTIVE_BLUR );
		imagefilter( $this->image, IMG_FILTER_GAUSSIAN_BLUR );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 15 );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, - 2 );

		return $this;
	}

}
