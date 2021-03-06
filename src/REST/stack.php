<?php

namespace andyp\pulsestack\REST;

use \WP_REST_Request;


class stack {

    public $posts;

    public $result;


    public function __construct()
    {
        $this->REST_call();
        $this->render();
    }



    /**
     * https://rudrastyh.com/wordpress/rest-api-get-posts.html
     */
    private function REST_call()
    {
        $transient = \get_transient( 'pulsestack' );
        if( ! empty( $transient ) ) {
            return $transient;
        }

        $response = \wp_remote_get( add_query_arg( array(
            'per_page' => 5,
            'pulse_category' => 4
        ), 'https://pulse.londonparkour.com/wp-json/wp/v2/pulse' ) );

        $this->posts = json_decode( $response['body'] ); // our posts are here

        \set_transient( 'pulsestack', json_decode( $this->posts ), DAY_IN_SECONDS );
    }


    public function render()
    {
        if (!isset($this->posts)){
            return;
        }

        shuffle($this->posts);

        $output = '<ul class="parkourpulse flex overflow-hidden h-96" style="width:400%">';

        foreach($this->posts as $key => $post )
        {
            // 1 = 'one', 12 = 'twelve'
            $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
            $id = $f->format($key);
            $zebra = ($key++%2==1) ? 'odd' : 'even';

            $output .= '<li class="'.$id.' '.$zebra.' w-1/5 h-1/2 lg:w-1/5 lg:h-2/3 p-8" style="transform: scaleX(1) scaleY(1);">';
                $output .= '<div class="bg-cover bg-center w-full h-full shadow lg:shadow-2xl rounded" style="background-image: url(\''.$post->imageURL.'\');" ></div>';
                $output .= '<div class="">'.$post->channelTitle.'</div>';
            $output .= '</li>';
        }

        $output .= '</ul>';

        $this->result = $output;
    }



    public function out()
    {
        return $this->result;
    }

}