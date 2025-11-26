<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class HomeController extends AbstractActionController
{
    public function indexAction()
    {
        // Lista temporária de filmes
        $filmes = [
            [
                'id' => 1,
                'nome' => "Um Sonho de Liberdade",
                'sinopse' => "Preso na década de 1940 pelo duplo homicídio de sua esposa e do amante dela, o íntegro banqueiro Andy Dufresne inicia uma nova vida na prisão de Shawshank, onde utiliza suas habilidades contábeis para trabalhar para um diretor amoral. Durante seu longo período na prisão, Dufresne passa a ser admirado pelos outros detentos – incluindo um prisioneiro mais velho chamado Red – por sua integridade e inabalável senso de esperança.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/sm/upload/7l/hn/46/uz/zGINvGjdlO6TJRu9wESQvWlOKVT-0-1000-0-1500-crop.jpg?v=8736d1c395",
                'anoLancamento' => 1994,
                'diretor' => "Frank Darabont",
                'atoresPrincipais' => "Tim Robbins, Morgan Freeman, Bob Gunton, William Sadler",
                'genero' => "Drama",
                'nota' => 4.62,
                'trailer' => "https://www.youtube.com/embed/PLl99DlL6b4?si=zo_DCD_JyPhI6LbJ",
                'streaming' => ["MAX"],
            ],
            [
                'id' => 2,
                'nome' => "O Poderoso Chefão",
                'sinopse' => "Don Vito Corleone comanda uma das maiores famílias mafiosas de Nova York. Quando uma tentativa de assassinato quase tira sua vida, seu filho Michael é forçado a assumir o império, mergulhando num mundo de poder, lealdade e traição.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/film-poster/5/1/8/1/8/51818-the-godfather-0-2000-0-3000-crop.jpg?v=bca8b67402",
                'anoLancamento' => 1972,
                'diretor' => "Francis Ford Coppola",
                'atoresPrincipais' => "AL Pacino, Marlon Brando, James Caan, Diane Keaton",
                'genero' => "Crime",
                'nota' => 4.58,
                'trailer' => "https://www.youtube.com/embed/88nc6NwAQG4?si=6baBARBkY2Pi16Mr",
                'streaming' => ["NETFLIX"],
            ],
            [
                'id' => 3,
                'nome' => "O Senhor dos Anéis: O Retorno do Rei",
                'sinopse' => "Sauron prepara ataque a Minas Tirith. Gandalf e Pippin partem para ajudar na defesa da capital de Gondor. Enquanto isso, Frodo, Sam e Gollum continuam sua jornada para destruir o Anel na Montanha da Perdição.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/sm/upload/zs/nt/u4/uz/xieWkPAgQrrk5wOyncayPd65hrp-0-1000-0-1500-crop.jpg?v=4c89d05285",
                'anoLancamento' => 2003,
                'diretor' => "Peter Jackson",
                'atoresPrincipais' => "Elijah Wood, Sean Astin, Karl Urban, Andy Serkis",
                'genero' => "Ficção Científica",
                'nota' => 4.57,
                'trailer' => "https://www.youtube.com/embed/r5X-hFf6Bwo?si=EOV6TTAut_5Zm6mS",
                'streaming' => ["MAX"],
            ],
            [
                'id' => 4,
                'nome' => "12 Homens e uma Sentença",
                'sinopse' => "A defesa e a acusação encerraram seus argumentos e o júri está entrando na sala para decidir se um jovem hispano-americano é culpado ou inocente do assassinato de seu pai. O que começa como um caso simples logo se transforma em um pequeno drama, revelando os preconceitos e ideias preconcebidas de cada jurado sobre o julgamento, o acusado e uns sobre os outros.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/film-poster/5/1/7/0/0/51700-12-angry-men-0-1000-0-1500-crop.jpg?v=b8aaf291a9",
                'anoLancamento' => 1957,
                'diretor' => "Sidney Lumet",
                'atoresPrincipais' => "Henry Fonda, Lee J. Cobb, Joseph Sweeney, Martin Balsam",
                'genero' => "Drama",
                'nota' => 4.57,
                'trailer' => "https://www.youtube.com/embed/TEN-2uTi2c0?si=lapV9AjXCOvXJ5oH",
                'streaming' => ["AMAZON"],
            ],
            [
                'id' => 5,
                'nome' => "O Poderoso Chefão: Parte 2",
                'sinopse' => "Na saga contínua da família criminosa Corleone, o jovem Vito Corleone cresce na Sicília e na Nova York da década de 1910. Na década de 1950, Michael Corleone tenta expandir os negócios da família para Las Vegas, Hollywood e Cuba.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/film-poster/5/1/8/1/6/51816-the-godfather-part-ii-0-1000-0-1500-crop.jpg?v=6a49853f25",
                'anoLancamento' => 1974,
                'diretor' => "Francis Ford Coppola",
                'atoresPrincipais' => "Al Pacino, Robert De Niro, Diane Keaton, James Caan",
                'genero' => "Crime",
                'nota' => 4.55,
                'trailer' => "https://www.youtube.com/embed/9O1Iy9od7-A?si=diLOgvyWjtIbMImJ",
                'streaming' => ["NETFLIX"],
            ],
            [
                'id' => 6,
                'nome' => "Batman: O Cavaleiro das Trevas",
                'sinopse' => "Batman eleva a aposta em sua guerra contra o crime. Com a ajuda do Tenente Jim Gordon e do Promotor Público Harvey Dent, Batman parte para desmantelar as organizações criminosas remanescentes que assolam as ruas. A parceria se mostra eficaz, mas logo eles se veem vítimas de um reinado de caos desencadeado por um gênio do crime em ascensão, conhecido pelos cidadãos aterrorizados de Gotham como o Coringa.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/sm/upload/78/y5/zg/ej/oefdD26aey8GPdx7Rm45PNncJdU-0-1000-0-1500-crop.jpg?v=2d0ce4be25",
                'anoLancamento' => 2008,
                'diretor' => "Christopher Nolan",
                'atoresPrincipais' => "Christian Bale, Heath Ledger, Gary Oldman, Aaron Eckhart",
                'genero' => "Ação",
                'nota' => 4.53,
                'trailer' => "https://www.youtube.com/embed/EXeTwQWrcwY?si=3d0KX35TMp6c9IB5",
                'streaming' => ["MAX"],
            ],
            [
                'id' => 7,
                'nome' => "A Lista de Schindler",
                'sinopse' => "A história verídica de como o empresário Oskar Schindler salvou mais de mil vidas judaicas dos nazistas enquanto trabalhavam como escravos em sua fábrica durante a Segunda Guerra Mundial.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/sm/upload/bz/1x/em/jr/yPisjyLweCl1tbgwgtzBCNCBle-0-1000-0-1500-crop.jpg?v=ca5215c5a9",
                'anoLancamento' => 1993,
                'diretor' => "Steven Spielberg",
                'atoresPrincipais' => "Liam Neeson, Ralph Fiennes, Ben Kingsley, Embeth Davidtz",
                'genero' => "Drama",
                'nota' => 4.52,
                'trailer' => "https://www.youtube.com/embed/GAf0nGq_FXQ?si=kOEzDze0WG3zJwGe",
                'streaming' => ["AMAZON"],
            ],
            [
                'id' => 8,
                'nome' => "O Senhor dos Anéis: A Sociedade do Anel",
                'sinopse' => "O jovem hobbit Frodo Bolseiro, após herdar um anel misterioso de seu tio Bilbo, precisa deixar sua casa para impedir que ele caia nas mãos de seu criador maligno. Ao longo do caminho, uma sociedade é formada para proteger o portador do anel e garantir que ele chegue ao seu destino final: a Montanha da Perdição, o único lugar onde pode ser destruído.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/sm/upload/3t/vq/0u/m6/1tX9ZlgVvWjAQhMs1vAfsYpi7VK-0-1000-0-1500-crop.jpg?v=30bbb824e1",
                'anoLancamento' => 2001,
                'diretor' => "Peter Jackson",
                'atoresPrincipais' => "Elijah Wood, Sean Astin, Ian McKellen, Viggo Mortensen",
                'genero' => "Ficção Científica",
                'nota' => 4.50,
                'trailer' => "https://www.youtube.com/embed/V75dMMIW2B4?si=rBBo6JZ8CqUke6uF",
                'streaming' => ["MAX"],
            ],
            [
                'id' => 9,
                'nome' => "Três Homens em Conflito",
                'sinopse' => "Enquanto a Guerra Civil se intensifica entre a União e a Confederação, três homens – um solitário quieto, um assassino implacável e um bandido mexicano – percorrem o sudoeste americano em busca de um cofre contendo US$ 200.000 em ouro roubado.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/film-poster/5/1/6/6/6/51666-the-good-the-bad-and-the-ugly-0-1000-0-1500-crop.jpg?v=9474a84e63",
                'anoLancamento' => 1966,
                'diretor' => "Sergio Leone",
                'atoresPrincipais' => "Clint Eastwood, Eli Wallach, Lee Van Cleef, Antonio Casale",
                'genero' => "Faroeste",
                'nota' => 4.50,
                'trailer' => "https://www.youtube.com/embed/WCN5JJY_wiA?si=ZEhAhDcPubfAcDi3",
                'streaming' => ["MUBI", "LOOKE"],
            ],
            [
                'id' => 10,
                'nome' => "O Senhor dos Anéis: As Duas Torres",
                'sinopse' => "Após a captura de Merry e Pippy pelos orcs, a Sociedade do Anel é dissolvida. Frodo e Sam seguem sua jornada rumo à Montanha da Perdição para destruir o anel e descobrem que estão sendo perseguidos pelo misterioso Gollum. Enquanto isso, Aragorn, o elfo e arqueiro Legolas e o anão Gimli partem para resgatar os hobbits sequestrados e chegam ao reino de Rohan, onde o rei Theoden foi vítima de uma maldição mortal de Saruman.",
                'capaPrincipal' => "https://a.ltrbxd.com/resized/film-poster/5/1/9/2/9/51929-the-lord-of-the-rings-the-two-towers-0-1000-0-1500-crop.jpg?v=9ef6c09783",
                'anoLancamento' => 2002,
                'diretor' => "Peter Jackson",
                'atoresPrincipais' => "Elijah Wood, Ian McKellen, Sean Astin, Viggo Mortensen",
                'genero' => "Ficção Científica",
                'nota' => 4.47,
                'trailer' => "https://www.youtube.com/embed/LbfMDwc4azU?si=I62cMg9XsV-F2UIY",
                'streaming' => ["MAX"],
            ],
        ];

        return new ViewModel([
            'filmes' => $filmes
        ]);
    }
}
