<?php
class N2SmartSliderPostBackgroundAnimationsModel
{

    public static $sets = array();
    public static $visualsBySet = array();
    public static $visuals = array();
    private static $data = array(
        'Default' => array(
            array(
                "id"    => 5,
                "value" => array(
                    'name' => 'Downscale',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5
                        ),
                        'to'       => array(
                            'scale' => 1.2
                        )
                    )
                )
            ),
            array(
                "id"    => 6,
                "value" => array(
                    'name' => 'Downscale top',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'y'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.2,
                            'y'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 7,
                "value" => array(
                    'name' => 'Downscale bottom',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'y'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.2,
                            'y'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 8,
                "value" => array(
                    'name' => 'Downscale left',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.2,
                            'x'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 9,
                "value" => array(
                    'name' => 'Downscale right',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.2,
                            'x'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 10,
                "value" => array(
                    'name' => 'Upscale',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.2
                        ),
                        'to'       => array(
                            'scale' => 1.5
                        )
                    )
                )
            ),
            array(
                "id"    => 11,
                "value" => array(
                    'name' => 'Upscale top',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.2,
                            'y'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'y'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 12,
                "value" => array(
                    'name' => 'Upscale bottom',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.2,
                            'y'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'y'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 13,
                "value" => array(
                    'name' => 'Upscale left',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.2,
                            'x'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 14,
                "value" => array(
                    'name' => 'Upscale right',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.2,
                            'x'     => 0

                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 15,
                "value" => array(
                    'name' => 'To top',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'y'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'y'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 16,
                "value" => array(
                    'name' => 'To bottom',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'y'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'y'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 17,
                "value" => array(
                    'name' => 'To left',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 18,
                "value" => array(
                    'name' => 'To right',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 15,
                "value" => array(
                    'name' => 'To top left',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0,
                            'y'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => 100,
                            'y'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 16,
                "value" => array(
                    'name' => 'To top right',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0,
                            'y'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => -100,
                            'y'     => 100
                        )
                    )
                )
            ),
            array(
                "id"    => 17,
                "value" => array(
                    'name' => 'To bottom left',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0,
                            'y'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => 100,
                            'y'     => -100
                        )
                    )
                )
            ),
            array(
                "id"    => 18,
                "value" => array(
                    'name' => 'To bottom right',
                    'data' => array(
                        'duration' => 5,
                        'from'     => array(
                            'scale' => 1.5,
                            'x'     => 0,
                            'y'     => 0
                        ),
                        'to'       => array(
                            'scale' => 1.5,
                            'x'     => -100,
                            'y'     => -100
                        )
                    )
                )
            )

        )
    );

    public static function init() {
        foreach (self::$data AS $setId => &$animations) {
            self::$sets[] = array(
                "id"           => $setId,
                "referencekey" => '',
                "value"        => $setId,
                "system"       => 1,
                "editable"     => 0
            );
            foreach ($animations AS &$animation) {
                $animation['referencekey']       = $setId;
                self::$visuals[$animation['id']] = $animation;
            }
            unset($animation);
        }
        unset($animations);

        self::$visualsBySet = self::$data;
    }
}

N2SmartSliderPostBackgroundAnimationsModel::init();
