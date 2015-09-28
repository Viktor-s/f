<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadTaxonomiesData as BaseLoadTaxonomiesData;

class LoadTaxonomiesData extends BaseLoadTaxonomiesData {

    protected function getHierarchy() {
        return [
            [
                'taxonomy' => [$this->defaultLocale => 'Category'],
                'taxons' => [
                    [
                        'taxon' => [$this->defaultLocale => 'Living Room'],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => 'Seating'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Occasional Tables'],
                                        'childs' => [
                                            [
                                                'taxon' => [$this->defaultLocale => 'Side Tables'],
                                            ],
                                            [
                                                'taxon' => [$this->defaultLocale => 'Сoffee Tables'],
                                            ],
                                            [
                                                'taxon' => [$this->defaultLocale => 'Console Tables'],
                                            ]
                                        ]
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Lounge Chairs']
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Sofas']
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Rocking Chairs']
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Ottomans, Poufs + Bean Bags']
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Benches']
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Low Stools']
                                    ]
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'Media + Storage'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Media Cabinets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'TV Stands + Mounts'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Credenzas'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Shelving + Bookcases'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Magazine Racks'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'living_Accents'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Indoor Fireplaces + Tools'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Clocks'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Mirrors'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Area Rugs'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Accent Pillows'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Throws + Blankets'],
                                    ],
                                ]
                            ]
                        ],
                    ],
                    [
                        'taxon' => [$this->defaultLocale => 'Kitchen'],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => 'Tabletop'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Dinnerware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Flatware + Place Settings'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Glassware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Serveware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Pitchers + Carafes'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'kitchen_Tabletop'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Dinnerware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Serveware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Flatware + Place Settings'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Glassware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Barware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Pitchers + Carafes'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Coffee + Tea'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Serve Sets + Accessories'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Kitchen + Table Linens'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'Utilities'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Cookware'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Kitchen Electronics'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Kitchen Tools + Gadgets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Food Containers + Storage'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Kitchen Faucets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'All Kitchen Sinks'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Undercabinet Lighting'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Switches, Dimmers + Outlets'],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => 'Dining'],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => 'dining_Furniture'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Dining Tables'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Extension Tables'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Credenzas'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Side + Dining Chairs'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bar + Counter Stools'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'Tabletop'],
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => 'Bedroom'],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => 'bedroom_Furniture'],
                                'childs' =>
                                [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Beds'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Daybeds'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Dressers'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bedside Tables'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'bedroom_Accents'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bedding'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Accent Pillows'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Throws + Blankets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Alarm Clocks'],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => 'Bath'],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => 'Fixtures'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Vanities'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Sinks'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Tubs'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Toilets + Bidets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Medicine Cabinets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Storage + Organization'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Shower Doors + Bases'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Shower Drains'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'Faucets'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Sink Faucets'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Shower Heads + Hand Showers'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Tub + Shower Systems'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Tub Fillers'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Trims'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Parts + Components'],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'bath_Accents'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bath Racks, Bars + Hooks'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bath Mirrors'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bath Accessories'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Towels'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bath Lighting'],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => 'Kids'],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => 'Nursery'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => 'Cribs'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Nursery Rockers'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Changing Tables'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Mattresses'],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => 'Bedding'],
                                    ],
                                ],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => 'Kids_Furniture'],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Bedroom"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Dressers"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Chairs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Tables + Play Sets"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Bookshelves + Storage"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Outdoor Furniture"],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Kids_Accents"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Decor + Accessories"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Rugs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Toys"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Kids' Wallpaper"],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => "Outdoor"],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => "Outdoor_Furniture"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Dining Tables"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Dining + Side Chairs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Bar + Counter Stools"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Sofas"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Chaise Lounges"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Lounge Chairs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Occasional Tables"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Benches, Low Stools + Ottomans"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Bar Carts + Serving Stations"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor_Kids' Outdoor Furniture"],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Outdoor_Accents"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Planters"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Pillows + Poufs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Rugs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Decor"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Umbrellas"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Outdoor Lighting"],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => "Workspace"],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => "Workspace_Seating"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Modern Office Chairs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Ergonomic Chairs"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Stools"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Conference + Side"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Stacking + Folding"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Lounge + Collaborative"],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Desks + Tables"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Desks"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Compact Desks"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Standing + Adjustable Desks"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Modular Desks"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Conference Tables"],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Storage + Accents"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Filing Cabinets + Storage"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Bookcases"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Desk Lamps"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Office Accessories"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Clocks"],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => "Storage"],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => "Storage_Furniture"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Media Cabinets"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "TV Stands + Mounts"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Credenzas"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Shelving + Bookcases"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Cabinets"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Dressers"],
                                    ],
                                ]
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Organizing + Containers"],
                                'childs' => [
                                    [
                                        'taxon' => [$this->defaultLocale => "Magazine Racks"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Space Savers, Wine Racks + Hooks"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Stepladders + Tools"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Screens + Curtains"],
                                    ],
                                    [
                                        'taxon' => [$this->defaultLocale => "Baskets, Bins + Boxes"],
                                    ],
                                ]
                            ]
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => "Lighting"],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => "Shop All Sale"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Pendants + Chandeliers"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Ceiling Lighting"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Wall Lighting"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Floor Lamps"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Table Lamps"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Desk Lamps"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Bath Lighting"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Ceiling Fans"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Outdoor Lighting"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Switches, Dimmers + Outlets"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Undercabinet Lighting"],
                            ],
                        ]
                    ],
                    [
                        'taxon' => [$this->defaultLocale => "Accents"],
                        'childs' => [
                            [
                                'taxon' => [$this->defaultLocale => "Accent Pillows"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Throws + Blankets"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Home Accents + Objects"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Mirrors"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Clocks"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Audio"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Candles + Candelabras"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Vases"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Flowerpots"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Picture Frames"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Screens + Curtains"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Watches + Jewelry"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Gift Guide"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Wallpaper + Wall Stickers"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Wall Decor"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Bath Accessories"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Baskets, Bins + Boxes"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Space Savers, Wine Racks + Hooks"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Ceiling Fans"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Air Purifiers"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Games + Recreation"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Pet Accessories"],
                            ],
                            [
                                'taxon' => [$this->defaultLocale => "Travel + Personal Accessories"],
                            ],
                        ]
                    ]
                ]
            ],
        ];
    }

    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {

        foreach ($this->getHierarchy() as $taxonomy) {
            $t = $this->createTaxonomy($taxonomy);
            $manager->persist($t);
        }

        $manager->flush();
    }

    protected function createTaxonomy(array $description) {
        /* @var $taxonomy TaxonomyInterface */
        $taxonomy = $this->getTaxonomyRepository()->createNew();

        foreach ($description['taxonomy'] as $locale => $name) {
            $taxonomy->setCurrentLocale($locale);
            $taxonomy->setFallbackLocale($locale);
            $taxonomy->setName($name);

            if ($this->defaultLocale === $locale) {
                //echo 'Taxonomy:' . $name;
                $this->setReference('Sylius.Taxonomy.' . $name, $taxonomy);
            }
        }

        if (array_key_exists('taxons', $description)) {
            foreach ($description['taxons'] as $taxon_description) {
                $this->createTaxon($taxon_description, $taxonomy);
            }
        }

        return $taxonomy;
    }

    protected function createTaxon(array $taxon_description, $taxonomy, $parent = false) {
        if (count($taxon_description) > 2) {
            print_r($taxon_description);
            throw new \Exception('Taxonomy description Error', 100500);
        }

        $taxon = $this->getTaxonRepository()->createNew();

        $taxonomy->addTaxon($taxon);
        foreach ($taxon_description['taxon'] as $locale => $name) {
            $taxon->setCurrentLocale($locale);
            $taxon->setFallbackLocale($locale);
            $taxon->setName($name);
            var_dump($name);
            if ($this->defaultLocale === $locale) {
                //echo 'Taxon:' . 'Sylius.Taxon.' . $name . PHP_EOL;
                $this->setReference('Sylius.Taxon.' . $name, $taxon);
            }
        }
        
        if($parent){
            echo 'Parent:'.$parent->getName();
            $parent->addChild($taxon);
            $taxon->setPermalink($parent->getName().'/'.$taxon->getName());
        }

        if (array_key_exists('childs', $taxon_description)) {
            foreach ($taxon_description['childs'] as $child_taxon_description) {
                $child_taxon = $this->createTaxon($child_taxon_description, $taxonomy, $taxon);
            }
        }

        return $taxon;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return 10;
    }

}
