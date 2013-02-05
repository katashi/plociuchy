<?php
class Song extends Hub {

	function Song($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null, $page = null) {
        $this->assign_template_titlecall($template, $title_call);
        //$this->ci->session->unset_userdata('paginate_song');
        //$this->ci->session->unset_userdata('filter_song');
        // paginate
        $paginate = $this->paginate_init($template, SONG_LIMIT, $this->load_all_ui_count(true));
        if (isset($page)) { $this->paginate_page_current_set($template, $page); } else { $page = $this->paginate_page_current_get($template); }
        $paginate_number = $this->paginate_from($this->paginate_get($template));
        $paginate = $this->paginate_get($template);
        $this->ci->smarty->assign('paginate', $paginate);
        $this->ci->smarty->assign('paginate_from', $paginate_number['from']);
        $this->ci->smarty->assign('paginate_to', $paginate_number['to']);
        $this->ci->smarty->assign('paginate_controller', $template);
        // song load
        $condition = array();
        $condition['limit'] = SONG_LIMIT;
        $condition['page'] = $paginate['page_current'];
        $song = $this->load_all_ui(serialize($condition), true);
        $song = $this->rate_compute($song, 0);
        $song_count = $this->load_all_ui_count(true);
        $this->ci->smarty->assign('song', $song);
        $this->ci->smarty->assign('song_count', $song_count);
        $this->ci->smarty->assign('filter_song', $this->ci->session->userdata('filter_song'));
        $this->ci->smarty->assign('genre', $this->genre_get_all(0));
        $this->smarty_display($template);
    }
    function display_redirect($template = null, $title_call = null, $variable = null) {
        $result = unserialize($variable);
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', $result['operation']);
        //
        $this->display('song');
    }
    function display_player($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->display_increment('kreomaniak_song', $id);
        $result = $this->load_ui($id);
        $this->ci->smarty->assign('song', $result['data']);
        $this->ci->smarty->assign('user', $result['user']);
        $this->ci->smarty->assign('file', $result['file']);
        $this->ci->smarty->assign('comment', $this->comment_load_all($id, 0));
        $this->ci->smarty->assign('rate', $this->rate_get($id, 0));
        $this->ci->smarty->assign('rate_enabled', $this->rate_enabled($id, 0));
        if ($result['data']) {
            $this->smarty_display($template.'/player');
        } else {
            $this->smarty_display($template.'/not_found');
        }
    }
    function display_add_hdd($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $this->smarty_display($template.'/add_hdd');
    }
    function display_add_live1($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 15;
        $condition['order'] = 'date_added';
        $this->ci->smarty->assign('arrange', $this->load_all_arrange_ui(serialize($condition)));
        $condition['limit'] = 15;
        $condition['order'] = 'date_added';
        $this->ci->smarty->assign('text', $this->load_all_text_ui(serialize($condition)));
        $genre_arrange = $this->genre_get_all(1);
        $this->ci->smarty->assign('genre_arrange', $genre_arrange);
        $genre_text = $this->genre_get_all(2);
        $this->ci->smarty->assign('genre_text', $genre_text);
        $this->smarty_display($template.'/add_live1');
    }
    function display_add_live2($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $this->ci->smarty->assign('arrange_file_name', $_POST['arrange_file_name']);
        $this->ci->smarty->assign('arrange_id', $_POST['arrange_id']);
        $this->ci->smarty->assign('arrange_id_file', $_POST['arrange_id_file']);
        $this->ci->smarty->assign('arrange_id_user', $_POST['arrange_id_user']);
        $this->ci->smarty->assign('text_id', $_POST['text_id']);
        $this->ci->smarty->assign('text_id_user', $_POST['text_id_user']);
        $result = $this->load_arrange_ui($_POST['arrange_id']);
        $this->ci->smarty->assign('arrange', $result['data']);
        $result = $this->load_text_ui($_POST['text_id']);
        $this->ci->smarty->assign('text', $result['data']);
        $this->smarty_display($template.'/add_live2');
    }
    function display_add_live3($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $this->ci->smarty->assign('arrange_id', $_POST['arrange_id']);
        $this->ci->smarty->assign('arrange_id_file', $_POST['arrange_id_file']);
        $this->ci->smarty->assign('arrange_id_user', $_POST['arrange_id_user']);
        $this->ci->smarty->assign('text_id', $_POST['text_id']);
        $this->ci->smarty->assign('text_id_user', $_POST['text_id_user']);
        $this->ci->smarty->assign('file_name', $_POST['file_name']);
        $this->ci->smarty->assign('author_arrange', $this->user_get('id', $_POST['arrange_id_user']));
        $this->ci->smarty->assign('author_text', $this->user_get('id', $_POST['text_id_user']));
        $genre = $this->genre_get_all(0);
        $this->ci->smarty->assign('genre', $genre);
        $this->smarty_display($template.'/add_live3');
    }

    // load
    function load_all_ui($condition = null, $filtrate = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/load_all_ui/'.$condition;
        $data = isset($filtrate) ? $this->filter_get() : null;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_ui_count($filtrate = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/load_all_ui_count';
        $data = isset($filtrate) ? $this->filter_get() : null;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_arrange_ui($condition = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_all_ui/'.$condition;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_text_ui($condition = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/load_all_ui/'.$condition;
        $result = $this->api_call($url);
        foreach($result['data'] as $key => $value) {
            $result['data'][$key]['text'] = str_replace('
', '<br>', $result['data'][$key]['text']);
            $result['data'][$key]['text'] = str_replace('"', '', $result['data'][$key]['text']);
        }
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_ui($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/load_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }
    function load_top_ui() {
        $url = CONSOLE_URL.'/kreomaniak:song/load_top_ui';
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_arrange_ui($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }
    function load_text_ui($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/load_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }

    // add
    function add($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/add_ui';
        $data = $_POST;
        $data['genre'] = $this->genre_get($_POST['id_genre']);
        $result = $this->api_call($url, $data, true);
        // smarty assign
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'song_add');
        // reduce points
        $this->point_reduce(2, 'song_add');
        // display
        $variable = array();
        $variable['success'] = $result['success'];
        $variable['code'] = $result['code'];
        $variable['operation'] = 'song_add';
        $variable = serialize($variable);
        $this->smarty_redirect_variables('song/display_redirect', 'song', $variable);
        //$song = new Song($this->ci);
        //$song->display('song');
    }
    function add_hdd($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/add_hdd_ui';
        $data = $_POST;
        $data['genre'] = $this->genre_get($_POST['id_genre']);
        $result = $this->api_call($url, $data, true);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'song_add_hdd');
        $this->smarty_display($template.'/add_hdd');
    }

    // filter
    function filter_get() {
        $filter = $this->ci->session->userdata('filter_song');
        if ($filter == '') {
            $this->filter_set_default();
            $filter = $this->ci->session->userdata('filter_song');
        }
        return $filter;
    }
    function filter_set($template = null, $title_call = null) {
        // lets prepare filter data
        $song_filter1 = isset($_POST['song_filter1']) ? $_POST['song_filter1'] : null;
        $song_filter2 = isset($_POST['song_filter2']) ? $_POST['song_filter2'] : null;
        $song_filter3 = isset($_POST['song_filter3']) ? $_POST['song_filter3'] : null;
        // setup values
        $filter_song = array();
        $filter_song['filter1'] = (int)$song_filter1;
        $filter_song['filter2'] = (int)$song_filter2;
        $filter_song['filter3'] = (int)$song_filter3;
        $this->ci->session->set_userdata('filter_song', $filter_song);
        // display template
        $this->smarty_redirect($template, $title_call);
    }
    function filter_set_default() {
        $filter_song = array();
        $filter_song['filter1'] = 0;
        $filter_song['filter2'] = 0;
        $filter_song['filter3'] = 0;
        $this->ci->session->set_userdata('filter_song', $filter_song);
    }
    function filter_delete() {
        $this->ci->session->unset_userdata('filter_song');
    }

    // transcode
    function transcode($template = null, $title_call = null, $video_file = null, $video_offset = null, $video_length = null, $arrange_file = null, $cam = null) {

        $ffmpegPath = '/usr/bin/ffmpeg';
        $soxPath = '/usr/bin/sox';
        $mediaPath =  '../media/';

        $file = $video_file;
        $offset = $video_offset;
        $duration = $video_length;
        $arrange = substr($arrange_file, 0, -4);

        echo 'Karaoke Recorder (Kreomaniak) - Transkodowanie filmu z podkladem<br>';
        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Nazwa pliku - '.$file.'<br>';
        echo 'Offset - '.$offset.'<br>';
        echo 'Dlugosc - '.$duration.'<br>';
        echo 'Aranz - '.$arrange.'<br>';

        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Pobieranie pliku z serwera FMS<br>';
        //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$file.'.flv">FLV ORIGINAL</a><br>';
        $local_file = $mediaPath.'transcode/'.$file.'.flv';
        $server_file = '/xkw9ys0a88e_apps/kreomaniak/streams/_definst_/'.$file.'.flv';
        $conn_id = ftp_connect('xkw9ys0a88e.rtmphost.com');
        $login_result = ftp_login($conn_id, 'xkw9ys0a88e', 'ryfunpredki2011');
        ftp_pasv($conn_id, true);
        if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
            //echo 'Plik skopiowany z FMS<br>';
        } else {
            //echo 'Wystapil problem';
            die;
        }
        ftp_close($conn_id);

        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Wydzielanie sciezki audio z pliku oryginalnego + normalizacja + mono do stereo<br>';
        //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$file.'.wav">VOCAL ORIGINAL</a><br>';
        //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$file.'_normalised.wav">VOCAL NORMALISED</a><br>';
        $source = $mediaPath.'transcode/'.$file.'.flv';
        $target = $mediaPath.'transcode/'.$file.'.wav';
        $target_normalised = $mediaPath.'transcode/'.$file.'_normalised.wav';
        exec($ffmpegPath ." -y -i ". $source ." -vn ". $target);
        exec($soxPath ." ". $target ." -c 2 ". $target_normalised ." --norm");
        unlink($mediaPath.'transcode/'.$file.'.wav');

        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Konwersja aranzu mp3 na wav + normalizacja (-4db)<br>';
        //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$arrange.'.wav">ARRANGE ORIGINAL</a><br>';
        //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$arrange.'_normalised.wav">ARRANGE NORMALISED</a><br>';
        $mp3 = $mediaPath.'file/'.$arrange.'.mp3';
        $wav = $mediaPath.'transcode/'.$arrange.'.wav';
        if ($offset > 0) { $duration_offset = $duration + ($offset/1000); } else { $duration_offset = $duration; }
        $wav_normalised = $mediaPath.'transcode/'.$arrange.'_normalised.wav';
        exec($ffmpegPath ." -y -t ".$duration_offset." -i ". $mp3 ." ". $wav);
        exec($soxPath ." ". $wav ." ". $wav_normalised ." gain -n -4");
        unlink($mediaPath.'transcode/'.$arrange.'.wav');

        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Operacje na aranzu, offset = '. $offset .'<br>';
        $source = $mediaPath.'transcode/'.$arrange.'_normalised.wav';
        $target = $mediaPath.'transcode/'.$arrange.'_corrected.wav';
        if ($offset > 0) {
            $offset = $offset / 1000;
            echo 'Przycinanie<br>';
            exec($soxPath . " ". $source ." ". $target ." trim ".$offset);
        } elseif ($offset < 0) {
            $offset = -($offset / 1000);
            echo 'Dodawanie ciszy<br>';
            exec($soxPath . " ". $source ." ". $target ." pad ".$offset."@0:00");
        } else {
            echo 'Bez zmian<br>';
            exec($soxPath . " ". $source ." ". $target);
        }
        unlink($mediaPath.'transcode/'.$arrange.'_normalised.wav');
        //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$arrange.'_corrected.wav">ARRANGE CORRECTED</a><br>';

        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Miksowanie - Aranz + Audio<br>';
        echo '<a href="http://kreomaniak.pl/media/transcode/'.$arrange.'_mix.wav">MIX</a><br>';
        // src1 - aranz
        // src2 - vocal
        $src1 = $mediaPath.'transcode/'.$arrange.'_corrected.wav';
        $src2 = $mediaPath.'transcode/'.$file.'_normalised.wav';
        $mix = $mediaPath.'transcode/'.$file.'_mixed.wav';
        exec($soxPath . " -m ". $src1 ." ". $src2 ." ". $mix ." trim 0 ".$duration." norm fade t 0 ".$duration." 5");
        unlink($mediaPath.'transcode/'.$arrange.'_corrected.wav');

        echo '-------------------------------------------------------------------------------------------<br>';
        echo 'Transkodowanie, Cam = '. $cam . '<br>';
        if ($cam == 0) {
            //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$file.'_final.flv">FLV FINAL ORIGINAL</a><br>';
            $src = $mediaPath.'transcode/'.$file.'_mixed.wav';
            $target = $mediaPath.'transcode/'.$file.'_final_'.$video_offset.'.flv';
            exec($ffmpegPath ." -y -t ".$duration." -i ". $src ." -vcodec copy ". $target);
        }
        if ($cam == 1) {
            //echo '<a href="http://csa2011.linuxpl.info/media/transcode/'.$file.'_final.flv">FLV FINAL ORIGINAL</a><br>';
            $src1 = $mediaPath.'transcode/'.$file.'_mixed.wav';
            $src2 = $mediaPath.'transcode/'.$file.'.flv';
            $target = $mediaPath.'transcode/'.$file.'_final_'.$video_offset.'.flv';
            exec($ffmpegPath ." -y -t ".$duration." -i ". $src1 ." -i ". $src2 ." -vcodec copy ". $target);
        }
        unlink($mediaPath.'transcode/'.$file.'_mixed.wav');
        unlink($mediaPath.'transcode/'.$file.'.flv');


        //echo '-------------------------------------------------------------------------------------------<br>';
        //echo 'Wgranie na serwer Fms<br>';
        $local_file = $mediaPath.'transcode/'.$file.'_final_'.$video_offset.'.flv';
        $server_file = '/xkw9ys0a88e_apps/kreomaniak/streams/_definst_/'.$file.'_final_'.$video_offset.'.flv';
        $conn_id = ftp_connect('xkw9ys0a88e.rtmphost.com');
        $login_result = ftp_login($conn_id, 'xkw9ys0a88e', 'ryfunpredki2011');
        ftp_pasv($conn_id, true);
        $result = ftp_put($conn_id, $server_file, $local_file, FTP_BINARY);
        ftp_close($conn_id);

        echo 'ok';

    }
}