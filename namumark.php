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

	function NamuMark(&$parser, &$text, &$strip_state) {
		$title = $parser -> getTitle();

		$text = preg_replace('/\n/', '<br>', $text);
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

		$text = preg_replace('/\[br\]/', '<br>', $text);
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

		$text = preg_replace('/{{\|((?:(?!\|}}).\n*)+)\|}}/', '<div style="border: 1px solid; padding: 10px;">$1</div>', $text);

		while(true) {
			$block_r = '/((?:<br>)(?:> ?(?:(?:(?!(?:<br>)).)+)?(?:<br>))+)/';
			if(preg_match($block_r, $text, $in_data)) {
				$block_data = $in_data[1];

				$block_data = preg_replace('/^(?:<br>)> ?/', '', $block_data);
				$block_data = preg_replace('/(?:<br>)> ?/', "<br>", $block_data);
				$block_data = preg_replace('/(?:<br>)$/', '', $block_data);

				$text = preg_replace($block_r, '<br><blockquote>'.$block_data.'</blockquote><br>', $text, 1);
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
						
						$li_data = preg_replace($sub_list_r, "\n".str_repeat("<star>", $data_len).$in_data[2], $li_data, 1);
					} else {
						break;
					}
				}

				$text = preg_replace($list_r, $li_data."\n", $text, 1);
			} else {
				break;
			}
		}

		$text = preg_replace('/<star>/', '*', $text);
		
		while(true) {
			$indent_r = '/<br>( +)/';
			if(preg_match($indent_r, $text, $in_data)) {
				$data_len = mb_strlen($in_data[1], 'utf-8');

				$text = preg_replace($indent_r, "\n".str_repeat(":", $data_len), $text, 1);
			} else {
				break;
			}
		}

		$text = preg_replace('/\[\[(http(?:s):\/\/(?:[^|]+))\|?((?:(?!\]\]).)+)\]\]/', '[$1 $2]', $text);

		$text = preg_replace('/^(?:<br>)+/', '', $text);
		$text = preg_replace('/(?:<br>)+$/', '', $text);
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