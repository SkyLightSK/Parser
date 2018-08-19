<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Item;

class Parser extends Command
{
    /**
     * Array that keeps parsed items.
     *
     * @var array
     */
    protected $items = array();

    /**
     * The array that consist of item names.
     *
     * @var array
     */
    protected $names = array();

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser {--migrate : Mirgate all parsed objects to db.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = config('parser');

        $migrate = $this->option('migrate');

        //Get links
        $links = $config['category_url'];

        foreach ($links as $link) {
            $fragments = array();
            $fragments_names = array();

            for( $i = 1 ; count($fragments) < $config['per_run']; $i++) {

                $elements = array();
                $elements_names = array();

                try {

                    $this->info(PHP_EOL . "Parsing: " . ($i == 1 ? $link : $link . 'page=' . $i));
                    // Get html remote text.
                    $html = file_get_contents($i == 1 ? $link : $link . 'page=' . $i);

                } catch (\Exception $e) {

                    $this->line('This URL does not exist.');

                    break;
                }

                if (isset($html)) {

                    // Create new instance for parser.
                    $crawler = new Crawler($html);

                    $items = $crawler->filter($config['item-link']);

                    $progressBar = $this->output->createProgressBar(count($items));
                    $progressBar->start();
                    foreach ($items as $key => $item) {

                        $progressBar->advance();
                        $config = config('parser');

                        if (count($fragments) + $key + 1 <= $config['per_run']) {


                            $item_url = $item->getAttribute('href');


                            $elements[$key]['link'] = $item->getAttribute('href');

                            // Get html remote text.
                            $html = file_get_contents($item_url);

                            // Create new instance for parser.
                            $item_crawler = new Crawler($html);

                            $name = $item_crawler->filter($config['item-name'])->count() > 0 ? $item_crawler->filter($config['item-name'])->text() : '';
                            $desc = $item_crawler->filter($config['item-desc'])->count() > 0 ? $item_crawler->filter($config['item-desc'])->text() : '';
                            $photo = $item_crawler->filter($config['item-photo'])->count() > 0 ?  $item_crawler->filter($config['item-photo'])->attr('src') : '';


                            $elements_names[$key]['name'] = trim($name);

                            $elements[$key]['name']         = trim($name);
                            $elements[$key]['description']  = trim($desc);
                            $elements[$key]['photo']        = $photo;
                        }
                    };
                    $progressBar->finish();

                    $fragments_names = array_merge($fragments_names, $elements_names);
                    $fragments = array_merge($fragments, $elements);
                }
            }

            $this->names = array_merge($this->names, $fragments_names);
            $this->items = array_merge($this->items, $fragments);

        }

        if(count($this->items)){
            $headers = ['Items'];
            $this->line( PHP_EOL );
            $this->table( $headers , $this->names );
        }

        if( count($this->items) && $migrate || count($this->items) && $this->confirm('Do you wish to mirgate this?') ){


            $this->question('Migrating...');

            foreach ($this->items as $key => $item){

                $item_db = Item::where('name' , $item['name'])->first();

                if($item_db === null){

                    $this->comment('migrate: '. $item['name']);

                    $new_item = new Item;
                    $new_item->name         = $item['name'];
                    $new_item->description  = $item['description'];
                    $new_item->link         = $item['link'];
                    $new_item->photo        = $item['photo'];
                    $new_item->save();

                    $this->info('migrated: '. $item['name']);

                }else{

                    if( $key+1 <= $config['update_per'] )
                    {
                        $this->comment('update : '. $item['name']);

                        Item::where('name', $item['name'])
                            ->update([
                                'name'          => $item['name'],
                                'description'   => $item['description'],
                                'link'          => $item['link'],
                                'photo'         => $item['photo']
                            ]);

                        $this->info('updated : '. $item['name']);
                    }

                }

            }

            $this->info('Finished');
        }

    }

}
