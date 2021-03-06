<?php
namespace Pernambuco;
use Subsite;
use MapasCulturais\App;

class Theme extends Subsite\Theme{

    protected static function _getTexts(){
        $app = App::i();
        $self = $app->view;
        $url_search_agents = $self->searchAgentsUrl;
        $url_search_spaces = $self->searchSpacesUrl;
        $url_search_events = $self->searchEventsUrl;
        $url_search_projects = $self->searchProjectsUrl;

        return [
//            'site: name' => App::i()->config['app.siteName'],
//            'site: description' => App::i()->config['app.siteDescription'],
//            'site: in the region' => 'na região',
//            'site: of the region' => 'da região',
//            'site: owner' => 'Secretaria',
//            'site: by the site owner' => 'pela Secretaria',
//
//            'home: title' => "Bem-vind@!",
//            'home: abbreviation' => "MC",
//            'home: colabore' => "Colabore com o Mapas Culturais",
//            'home: welcome' => "O Mapas Culturais é uma plataforma livre, gratuita e colaborativa de mapeamento cultural.",
//            'home: events' => "Você pode pesquisar eventos culturais nos campos de busca combinada. Como usuário cadastrado, você pode incluir seus eventos na plataforma e divulgá-los gratuitamente.",
//            'home: agents' => "Você pode colaborar na gestão da cultura com suas próprias informações, preenchendo seu perfil de agente cultural. Neste espaço, estão registrados artistas, gestores e produtores; uma rede de atores envolvidos na cena cultural da região. Você pode cadastrar um ou mais agentes (grupos, coletivos, bandas instituições, empresas, etc.), além de associar ao seu perfil eventos e espaços culturais com divulgação gratuita.",
//            'home: spaces' => "Procure por espaços culturais incluídos na plataforma, acessando os campos de busca combinada que ajudam na precisão de sua pesquisa. Cadastre também os espaços onde desenvolve suas atividades artísticas e culturais.",
//            'home: projects' => "Reúne projetos culturais ou agrupa eventos de todos os tipos. Neste espaço, você encontra leis de fomento, mostras, convocatórias e editais criados, além de diversas iniciativas cadastradas pelos usuários da plataforma. Cadastre-se e divulgue seus projetos.",
//            'home: home_devs' => 'Existem algumas maneiras de desenvolvedores interagirem com o Mapas Culturais. A primeira é através da nossa <a href="https://github.com/hacklabr/mapasculturais/blob/master/doc/api.md" target="_blank">API</a>. Com ela você pode acessar os dados públicos no nosso banco de dados e utilizá-los para desenvolver aplicações externas. Além disso, o Mapas Culturais é construído a partir do sofware livre <a href="http://institutotim.org.br/project/mapas-culturais/" target="_blank">Mapas Culturais</a>, criado em parceria com o <a href="http://institutotim.org.br" target="_blank">Instituto TIM</a>, e você pode contribuir para o seu desenvolvimento através do <a href="https://github.com/hacklabr/mapasculturais/" target="_blank">GitHub</a>.',
//
//            'search: verified results' => 'Resultados Verificados',
//            'search: verified' => "Verificados"
        ];
    }

    static function getThemeFolder() {
        return __DIR__;
    }

    function _init() {
        parent::_init();
        $app = App::i();
        $app->hook('view.render(<<*>>):before', function() use($app) {
            $this->_publishAssets();
        });

        $that = $this;


        $app->hook('subsite.applyConfigurations:after', function(&$config) use($that){
            $theme_path = $that::getThemeFolder() . '/';
            if (file_exists($theme_path . 'conf-base.php')) {
                $theme_config = require $theme_path . 'conf-base.php';
                $config = array_merge($config, $theme_config);
            }
            if (file_exists($theme_path . 'config.php')) {
                $theme_config = require $theme_path . 'config.php';
                $config = array_merge($config, $theme_config);
            }
        });

    }

    protected function _getFilters(){
        $en_municipio_filter = [
            'fieldType' => 'text',
            'label' => 'Município',
            'isInline' => false,
            'isArray' => false,
            'placeholder' => 'Selecione os Municípios',
            'filter' => [
                'param' => 'En_Municipio',
                'value' => 'ILIKE(*{val}*)'
            ]
        ];

        $ent_filters = parent::_getFilters();
        $mod_filters = [];
        foreach ($ent_filters as $entity => $filters) {
            $mod_filters[$entity] = [];
            if (in_array($entity, ['space', 'agent'])){
                $mod_filters[$entity][] = $en_municipio_filter;
            }
            foreach ($filters as $filter)
                if (!(isset($filter['fieldType']) && $filter['fieldType'] === 'checkbox-verified'))
                    $mod_filters[$entity][] = $filter;
        }

        return $mod_filters;

}
}
