<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaintsController extends Controller
{
    /**
     * Get list of saints for the carousel (fixed data)
     */
    public function index(Request $request): JsonResponse
    {
        $saints = [
            [
                'id' => 1,
                'name' => 'Peter',
                'feastDay' => 'June 29',
                'image' => '/images/saints/imgi_12_St.-Peter.jpg',
                'description' => 'Saint Peter, also known as Simon Peter, was one of the Twelve Apostles of Jesus Christ and the first Pope of the Catholic Church.',
                'filename' => 'imgi_12_St.-Peter.jpg',
            ],
            [
                'id' => 2,
                'name' => 'Augustine',
                'feastDay' => 'August 28',
                'image' => '/images/saints/imgi_8_St.-Augustine.jpg',
                'description' => 'Saint Augustine of Hippo was a theologian and philosopher who became one of the most important figures in the development of Western Christianity.',
                'filename' => 'imgi_8_St.-Augustine.jpg',
            ],
            [
                'id' => 3,
                'name' => 'Anthony',
                'feastDay' => 'June 13',
                'image' => '/images/saints/imgi_9_St.-Anthony.jpg',
                'description' => 'Saint Anthony of Padua was a Portuguese Catholic priest and friar of the Franciscan Order, known for his powerful preaching and miracles.',
                'filename' => 'imgi_9_St.-Anthony.jpg',
            ],
            [
                'id' => 4,
                'name' => 'Lawrence',
                'feastDay' => 'August 10',
                'image' => '/images/saints/imgi_17_St.-Lawrence.jpg',
                'description' => 'Saint Lawrence was one of the seven deacons of the city of Rome, martyred during the persecution of Emperor Valerian.',
                'filename' => 'imgi_17_St.-Lawrence.jpg',
            ],
            [
                'id' => 5,
                'name' => 'Faustina',
                'feastDay' => 'October 5',
                'image' => '/images/saints/imgi_10_St.-Faustina.jpg',
                'description' => 'Saint Faustina Kowalska was a Polish nun and mystic who received visions of Jesus and promoted the Divine Mercy devotion.',
                'filename' => 'imgi_10_St.-Faustina.jpg',
            ],
            [
                'id' => 6,
                'name' => 'Andrew',
                'feastDay' => 'November 30',
                'image' => '/images/saints/imgi_14_St.-Andrew.jpg',
                'description' => 'Saint Andrew was one of the Twelve Apostles of Jesus Christ and the brother of Saint Peter. He is the patron saint of Scotland.',
                'filename' => 'imgi_14_St.-Andrew.jpg',
            ],
            [
                'id' => 7,
                'name' => 'Francis Xavier',
                'feastDay' => 'December 3',
                'image' => '/images/saints/imgi_15_St.-Francis-Xavier.jpg',
                'description' => 'Saint Francis Xavier was a Jesuit missionary who spread Christianity in Asia, particularly in India, Japan, and the East Indies.',
                'filename' => 'imgi_15_St.-Francis-Xavier.jpg',
            ],
            [
                'id' => 8,
                'name' => 'Blaise',
                'feastDay' => 'February 3',
                'image' => '/images/saints/imgi_16_St.-Blaise.jpg',
                'description' => 'Saint Blaise was a physician and bishop of Sebastea who is venerated as the patron saint of throat ailments.',
                'filename' => 'imgi_16_St.-Blaise.jpg',
            ],
            [
                'id' => 9,
                'name' => 'Anne',
                'feastDay' => 'July 26',
                'image' => '/images/saints/imgi_20_St.-Anne.jpg',
                'description' => 'Saint Anne is traditionally the mother of the Virgin Mary and grandmother of Jesus Christ, though not mentioned in the canonical gospels.',
                'filename' => 'imgi_20_St.-Anne.jpg',
            ],
            [
                'id' => 10,
                'name' => 'Gonsalo Garcia',
                'feastDay' => 'February 6',
                'image' => '/images/saints/imgi_23_St.-Gonsalo-Garcia.jpg',
                'description' => 'Saint Gonsalo Garcia was a Franciscan friar and martyr who was crucified in Japan for his Christian faith.',
                'filename' => 'imgi_23_St.-Gonsalo-Garcia.jpg',
            ],
            [
                'id' => 11,
                'name' => 'Maria Goretti',
                'feastDay' => 'July 6',
                'image' => '/images/saints/imgi_19_St.-Maria-Goretti.jpg',
                'description' => 'Saint Maria Goretti was an Italian virgin martyr who died defending her chastity and is known as the patron saint of purity.',
                'filename' => 'imgi_19_St.-Maria-Goretti.jpg',
            ],
            [
                'id' => 12,
                'name' => 'Sebastian',
                'feastDay' => 'January 20',
                'image' => '/images/saints/imgi_26_St.-Sebastian.jpg',
                'description' => 'Saint Sebastian was a Roman soldier who was martyred for his Christian faith and is often depicted with arrows.',
                'filename' => 'imgi_26_St.-Sebastian.jpg',
            ],
            [
                'id' => 13,
                'name' => 'John the Baptist',
                'feastDay' => 'June 24',
                'image' => '/images/saints/imgi_27_St.-John-the-Baptist.jpg',
                'description' => 'Saint John the Baptist was a Jewish preacher who baptized Jesus and is considered a prophet in Christianity.',
                'filename' => 'imgi_27_St.-John-the-Baptist.jpg',
            ],
            [
                'id' => 14,
                'name' => 'Thomas',
                'feastDay' => 'July 3',
                'image' => '/images/saints/imgi_24_St.-Thomas.jpg',
                'description' => 'Saint Thomas was one of the Twelve Apostles of Jesus Christ, known for his initial doubt about the Resurrection.',
                'filename' => 'imgi_24_St.-Thomas.jpg',
            ],
            [
                'id' => 15,
                'name' => 'Christopher',
                'feastDay' => 'July 25',
                'image' => '/images/saints/imgi_13_St.-Christopher.jpg',
                'description' => 'Saint Christopher is venerated as a martyr and is considered the patron saint of travelers and motorists.',
                'filename' => 'imgi_13_St.-Christopher.jpg',
            ],
            [
                'id' => 16,
                'name' => 'Paul',
                'feastDay' => 'June 29',
                'image' => '/images/saints/imgi_25_St.-Paul.jpg',
                'description' => 'Saint Paul was an apostle who spread the teachings of Jesus Christ and wrote many of the New Testament epistles.',
                'filename' => 'imgi_25_St.-Paul.jpg',
            ],
            [
                'id' => 17,
                'name' => 'Theresa of Child Jesus',
                'feastDay' => 'October 1',
                'image' => '/images/saints/imgi_11_St.-Theresa-of-Child-Jesus.jpg',
                'description' => 'Saint Therese of Lisieux, also known as the Little Flower, was a French Carmelite nun known for her "Little Way" of spiritual childhood.',
                'filename' => 'imgi_11_St.-Theresa-of-Child-Jesus.jpg',
            ],
            [
                'id' => 18,
                'name' => 'Vincent De Paul',
                'feastDay' => 'September 27',
                'image' => '/images/saints/imgi_18_St.-Vincent-De-Paul.jpg',
                'description' => 'Saint Vincent de Paul was a French priest who dedicated his life to serving the poor and founded the Vincentians.',
                'filename' => 'imgi_18_St.-Vincent-De-Paul.jpg',
            ],
            [
                'id' => 19,
                'name' => 'Michael',
                'feastDay' => 'September 29',
                'image' => '/images/saints/imgi_29_St.-Michael.jpg',
                'description' => 'Saint Michael the Archangel is a powerful angel who is considered the protector of the Church and the patron of soldiers.',
                'filename' => 'imgi_29_St.-Michael.jpg',
            ],
            [
                'id' => 20,
                'name' => 'Martin',
                'feastDay' => 'November 11',
                'image' => '/images/saints/imgi_21_St.-Martin.jpg',
                'description' => 'Saint Martin of Tours was a bishop who is known for cutting his cloak in half to share with a beggar.',
                'filename' => 'imgi_21_St.-Martin.jpg',
            ],
            [
                'id' => 21,
                'name' => 'Dominic Savio',
                'feastDay' => 'March 9',
                'image' => '/images/saints/imgi_28_St.-Dominic-Savio.jpg',
                'description' => 'Saint Dominic Savio was a young Italian student of Saint John Bosco who died at the age of 14 and is known for his piety.',
                'filename' => 'imgi_28_St.-Dominic-Savio.jpg',
            ],
            [
                'id' => 22,
                'name' => 'Holy Family',
                'feastDay' => 'December 30',
                'image' => '/images/saints/imgi_30_St.-Holy-Family.jpg',
                'description' => 'The Holy Family consists of Jesus, Mary, and Joseph, serving as a model of family life and Christian virtues.',
                'filename' => 'imgi_30_St.-Holy-Family.jpg',
            ],
            [
                'id' => 23,
                'name' => 'Jude',
                'feastDay' => 'October 28',
                'image' => '/images/saints/imgi_22_St.-Jude.jpg',
                'description' => 'Saint Jude Thaddeus was one of the Twelve Apostles and is known as the patron saint of lost causes and desperate situations.',
                'filename' => 'imgi_22_St.-Jude.jpg',
            ],
        ];

        // If a specific saint is requested
        if ($request->has('id')) {
            $saintId = $request->get('id');
            $saint = collect($saints)->firstWhere('id', $saintId);

            if ($saint) {
                return response()->json($saint);
            }

            return response()->json(['error' => 'Saint not found'], 404);
        }

        // Return all saints for carousel
        return response()->json($saints);
    }

    /**
     * Get saint of the day (fixed data)
     */
    public function saintOfTheDay(Request $request): JsonResponse
    {
        $saintOfTheDay = [
            'id' => 1,
            'name' => 'Peter',
            'feastDay' => 'June 29',
            'image' => '/images/saints/imgi_12_St.-Peter.jpg',
            'description' => 'Saint Peter, also known as Simon Peter, was one of the Twelve Apostles of Jesus Christ and the first Pope of the Catholic Church.',
            'filename' => 'imgi_12_St.-Peter.jpg',
        ];

        return response()->json($saintOfTheDay);
    }

    /**
     * Get saint image by name (fixed data)
     */
    public function getSaintImageByName(Request $request): JsonResponse
    {
        $saintName = $request->get('name');

        if (! $saintName) {
            return response()->json(['error' => 'Saint name is required'], 400);
        }

        return response()->json([
            'saint_name' => $saintName,
            'image_url' => '/images/saints/default-saint.jpg',
            'source' => 'fixed_data',
        ]);
    }
}
