<?php
	// Take credit for your work.
	$wgExtensionCredits['parserhook'][] = array(
		'path' => __FILE__,
		'name' => '미디어위키-나무마크(오리마크)',
		'description' => '미디어위키에서 나무마크를 사용가능하게 합니다.',
		'version' => '2.0.0.0-Beta',
		'author' => 'koreapyj 원본, 김동동 수정, 2DU 재 설계',
		'url' => 'https://github.com/2DU/PHP-OriMark',
		'license-name' => "AGPL-3.0",
	);

	$wgHooks['ParserBeforeStrip'][] = 'NamuMark';
	$wgHooks['InternalParseBeforeLinks'][] = 'NamuMarkHTML';
	$wgHooks['ParserBeforeTidy'][] = 'NamuMarkHTML2';
	$wgHooks['ParserAfterTidy'][] = 'NamuMarkExtraHTML';

	function j_print($data) {
		echo '<script>console.log("'.preg_replace('/(\(|\)|\'|\"|\n)/', '\\$1', $data).'");</script>';
	}

	function table_parser($data, $cel_data, $start_data, $num = 0) {
		$table_class = 'class="';
		$all_table = 'style="';
		$cel_style = 'style="';
		$row_style = 'style="';
		$row = '';
		$cel = '';

		if(preg_match("/<table ?width=((?:(?!>).)*)>/", $data, $in_data)) {
			if(preg_match("/^[0-9]+$/", $in_data[1])) {
				$all_table = $all_table.'width: '.$in_data[1].'px;';
			} else {
				$all_table = $all_table.'width: '.$in_data[1].';';
			}
		}
		
		if(preg_match("/<table ?height=((?:(?!>).)*)>/", $data, $in_data)) {
			if(preg_match("/^[0-9]+$/", $in_data[1])) {
				$all_table = $all_table.'height: '.$in_data[1].'px;';
			} else {
				$all_table = $all_table.'height: '.$in_data[1].';';
			}
		}

		if(preg_match("/<table ?align=((?:(?!>).)*)>/", $data, $in_data)) {
			if($in_data[1] == 'right') {
				$all_table = $all_table.'float: right;';
			} else if($in_data[1] == 'center') {
				$all_table = $all_table.'margin: auto;';
			}
		}

		if(preg_match("/<table ?textalign=((?:(?!>).)*)>/", $data, $in_data)) {
			$num = 1;

			if($in_data[1] == 'right') {
				$all_table = $all_table.'text-align: right;';
			} else if($in_data[1] == 'center') {
				$all_table = $all_table.'text-align: center;';
			}
		}

		if(preg_match("/<row ?textalign=((?:(?!>).)*)>/", $data, $in_data)) {
			if($in_data[1] == 'right') {
				$row_style = $row_style.'text-align: right;';
			} else if($in_data[1] == 'center') {
				$row_style = $row_style.'text-align: center;';
			} else {
				$row_style = $row_style.'text-align: left;';
			}
		}
		
		if(preg_match("/<-((?:(?!>).)*)>/", $data, $in_data)) {
			$cel = 'colspan="'.$in_data[1].'"';
		} else {
			$cel = 'colspan="'.(string)(int)(mb_strlen($start_data) / 2).'"';
		}
	
		if(preg_match("/<\|((?:(?!>).)*)>/", $data, $in_data)) {
			$row = 'rowspan="'.$in_data[1].'"';
		}
	
		if(preg_match("/<rowbgcolor=(#(?:[0-9a-f-A-F]{3}){1,2}|\w+)>/", $data, $in_data)) {
			$row_style = $row_style.'background: '.$in_data[1].';';
		}
			
		if(preg_match("/<table ?bordercolor=(#(?:[0-9a-f-A-F]{3}){1,2}|\w+)>/", $data, $in_data)) {
			$all_table = $all_table.'border: '.$in_data[1].' 2px solid;';
		}
			
		if(preg_match("/<table ?bgcolor=(#(?:[0-9a-f-A-F]{3}){1,2}|\w+)>/", $data, $in_data)) {
			$all_table = $all_table.'background: '.$in_data[1].';';
		}
			
		if(preg_match("/<(?:bgcolor=)?(#(?:[0-9a-f-A-F]{3}){1,2}|\w+)>/", $data, $in_data)) {
			$cel_style = $cel_style.'background: '.$in_data[1].';';
		}
			
		if(preg_match("/<width=((?:(?!>).)*)>/", $data, $in_data)) {
			$cel_style = $cel_style.'width: '.$in_data[1].'px;';
		}
	
		if(preg_match("/<height=((?:(?!>).)*)>/", $data, $in_data)) {
			$cel_style = $cel_style.'height: '.$in_data[1].'px;';
		}
			
		$text_right = preg_match("/<\)>/", $data, $in_data_1);
		$text_center = preg_match("/<:>/", $data, $in_data_2);
		$text_left = preg_match("/<\(>/", $data, $in_data_3);
		if($text_right) {
			$cel_style = $cel_style.'text-align: right;';
		} else if($text_center) {
			$cel_style = $cel_style.'text-align: center;';
		} else if($text_left) {
			$cel_style = $cel_style.'text-align: left;';
		} else if($num == 0) { 
			if(preg_match("/^ /", $cel_data) && preg_match("/ $/", $cel_data)) {
				$cel_style = $cel_style.'text-align: center;';
			} else if(preg_match("/^ /", $cel_data)) {
				$cel_style = $cel_style.'text-align: right;';
			} else if(preg_match("/ $/", $cel_data)) {
				$cel_style = $cel_style.'text-align: left;';
			}
		}
			
		$all_table = $all_table.'"';
		$cel_style = $cel_style.'"';
		$row_style = $row_style.'"';
		$table_class = $table_class.'"';
	
		return array($all_table, $row_style, $cel_style, $row, $cel, $table_class, $num);
	}

	function table_start($data) {
		while(true) {
			if(preg_match('/\n((?:(?:(?:(?:\|\|)+(?:(?:(?!\|\|).(?:\n)*)*))+)\|\|(?:\n)?)+)/', $data, $in_data)) {
				$table = $in_data[1];

				while(true) {
					if(preg_match('/^((?:\|\|)+)((?:<(?:(?:(?!>).)+)>)*)\n*((?:(?!\|\|).\n*)*)/', $table, $in_in_data)) {
						$return_table = table_parser($in_in_data[2], $in_in_data[3], $in_in_data[1]);
						$num = $return_table[6];

						$table = preg_replace('/^((?:\|\|)+)((?:<(?:(?:(?!>).)+)>)*)\n*/', "[nobr]".'{| class="wikitable" '.$return_table[0]."[nobr]".'|- '.$return_table[1]."[nobr]".'| '.$return_table[2].' '.$return_table[3].' '.$return_table[4].' | ', $table, 1);
					} else {
						break;
					}
				}

				$table = preg_replace('/\|\|\n?$/', "[nobr]".'|}'."[nobr]", $table);

				while(true) {
					if(preg_match('/\|\|\n((?:\|\|)+)((?:<(?:(?:(?!>).)+)>)*)\n*((?:(?!\|\||<\/td>).\n*)*)/', $table, $in_in_data)) {
						$return_table = table_parser($in_in_data[2], $in_in_data[3], $in_in_data[1], $num);

						$table = preg_replace('/\|\|\n((?:\|\|)+)((?:<(?:(?:(?!>).)+)>)*)\n*/', "[nobr]".'|- '.$return_table[1]."[nobr]".'| '.$return_table[2].' '.$return_table[3].' '.$return_table[4].' | ', $table, 1);
					} else {
						break;
					}
				}

				while(true) {
					if(preg_match('/((?:\|\|)+)((?:<(?:(?:(?!>).)+)>)*)\n*((?:(?:(?!\|\||<\/td>).)|\n)*\n*)/', $table, $in_in_data)) {
						$return_table = table_parser($in_in_data[2], $in_in_data[3], $in_in_data[1], $num);

						$table = preg_replace('/((?:\|\|)+)((?:<(?:(?:(?!>).)+)>)*)\n*/', "[nobr]".'| '.$return_table[2].' '.$return_table[3].' '.$return_table[4].' | ', $table, 1);
					} else {
						break;
					}
				}

				$data = preg_replace('/\n((?:(?:(?:(?:\|\|)+(?:(?:(?!\|\|).(?:\n)*)*))+)\|\|(?:\n)?)+)/', $table, $data, 1);
			} else {
				break;
			}
		}

		return $data;
	}

	function NamuMark(&$parser, &$text, &$strip_state) {
		$title = $parser -> getTitle();

		$text = preg_replace('/\n/', "<br>", $text);
		$text = "<br>".$text."<br>";

		while(true) {
			$include_r = '/\[include\(((?:(?!\)\]).)+)\)\]/';
			if(preg_match($include_r, $text, $in_data)) {
				$text = preg_replace($include_r, '{{'.preg_replace('/, ?/', '|', $in_data[1]).'}}', $text, 1);
			} else {
				break;
			}
		}

		$text = preg_replace('/__((?:(?!__).)+)__/', '<u>$1</u>', $text);
		$text = preg_replace('/--((?:(?!--).)+)--/', '<s>$1</s>', $text);
		$text = preg_replace('/~~((?:(?!~~).)+)~~/', '<s>$1</s>', $text);
		$text = preg_replace('/\^\^((?:(?!\^\^).)+)\^\^/', '<sup>$1</sup>', $text);
		$text = preg_replace('/,,((?:(?!,,).)+),,/', '<sub>$1</sub>', $text);

		$text = preg_replace('/\[목차\]/', '__TOC__', $text);

		$text = preg_replace('/\[br\]/', "<br>", $text);

		$text = preg_replace('/\[date\]/', date("Y-m-d H:i:s", time()), $text);
		
		while(true) {
			$youtube_r = '/\[(youtube|kakaotv|nicovideo)\(((?:(?!\)\]).)+)\)\]/';
			if(preg_match($youtube_r, $text, $in_data)) {
				preg_match('/^([^,]+)/', $in_data[2], $code);
				preg_match('/width=([0-9]+%?)/', $in_data[2], $width);
				preg_match('/height=([0-9]+%?)/', $in_data[2], $height);

				if($in_data[1] != 'kakaotv') {
					$text = preg_replace($youtube_r, '<'.$in_data[1].' width="'.$width[1].'" height="'.$height[1].'">'.$code[1].'</'.$in_data[1].'>', $text, 1);
				} else {
					$text = preg_replace($youtube_r, '{{#tag:tvpot|'.$code[1].'|width='.$width[1].'|height="'.$height[1].'"}}', $text, 1);
				}
			} else {
				break;
			}
		}

		$text = preg_replace('/{{\|((?:(?!\|}}).(?:<br>)*)+)\|}}/', '<div style="border: 1px solid; padding: 10px;">$1</div>', $text);

		while(true) {
			$block_r = '/((?:<br>)(?:> ?(?:(?:(?!(?:<br>)).)+)?(?:<br>))+)/';
			if(preg_match($block_r, $text, $in_data)) {
				$block_data = $in_data[1];

				$block_data = preg_replace('/^(?:<br>)> ?/', '', $block_data);
				$block_data = preg_replace('/(?:<br>)> ?/', "<br>", $block_data);
				$block_data = preg_replace('/(?:<br>)$/', '', $block_data);

				$text = preg_replace($block_r, "<br>".'<blockquote>'.$block_data.'</blockquote>'."<br>", $text, 1);
			} else {
				break;
			}
		}

		while(true) {
			$list_r = '/((?:<br>)(?:(?: *)\* ?(?:(?:(?!(?:<br>)).)+)(?:<br>))+)/';
			if(preg_match($list_r, $text, $in_data)) {
				$li_data = $in_data[1];
				
				while(true) {
					$sub_list_r = '/(?:<br>)(?:( *)\* ?((?:(?!(?:<br>)).)+))/';	
					if(preg_match($sub_list_r, $li_data, $in_data)) {
						$data_len = mb_strlen($in_data[1], 'utf-8');
						if($data_len == 0) {
							$data_len = 1;
						}
						
						$li_data = preg_replace($sub_list_r, "[nobr]".str_repeat("<star>", $data_len).$in_data[2], $li_data, 1);
					} else {
						break;
					}
				}

				$text = preg_replace($list_r, $li_data."[nobr]", $text, 1);
			} else {
				break;
			}
		}

		$text = preg_replace('/<star>/', '*', $text);
		
		while(true) {
			$indent_r = '/(?:<br>)( +)/';
			if(preg_match($indent_r, $text, $in_data)) {
				$data_len = mb_strlen($in_data[1], 'utf-8');

				$text = preg_replace($indent_r, "[nobr]".str_repeat(":", $data_len), $text, 1);
			} else {
				break;
			}
		}

		$text = preg_replace('/(?:<br>)/', "\n", $text);
		$text = table_start($text);
		$text = preg_replace('/\n/', "<br>", $text);

		$text = preg_replace('/\[\[(http(?:s):\/\/(?:[^|]+))\|?((?:(?!\]\]).)+)\]\]/', '[$1 $2]', $text);

		$text = preg_replace('/^(?:<br>)+/', '', $text);
		$text = preg_replace('/(?:<br>)+$/', '', $text);
		
		$text = preg_replace('/\[nobr\]/', "\n", $text);
	}

	function NamuMarkHTML(Parser &$parser, &$text) {
		$title = $parser->getTitle();
	}

	function NamuMarkHTML2(&$parser, &$text) {
		$title = $parser->getTitle();
	}

	function NamuMarkExtraHTML(&$parser, &$text) {
		$title = $parser->getTitle();
	}
?>