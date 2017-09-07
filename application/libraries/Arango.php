<?php
/**
 * Created by PhpStorm.
 * User: sunil
 * Date: 6/9/17
 * Time: 10:13 AM
 */

require 'arangodb-php/autoload.php';

use ArangoDBClient\Collection as ArangoCollection;
use ArangoDBClient\CollectionHandler as ArangoCollectionHandler;
use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\ConnectionOptions as ArangoConnectionOptions;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;
use ArangoDBClient\Document as ArangoDocument;
use ArangoDBClient\Exception as ArangoException;
use ArangoDBClient\Export as ArangoExport;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use ArangoDBClient\Statement as ArangoStatement;
use ArangoDBClient\UpdatePolicy as ArangoUpdatePolicy;


class Arango extends ArangoCollection{
    public $connection;
    public $dbconfig;
    public function __construct(){
        $ci = & get_instance();
        $ci->config->load('arangodb');
        $this->dbconfig = $ci->config->item('connection');
    }


    //Returns the connection Object
    public function connect(){
        $connectionOptions = [
            // database name
            ArangoConnectionOptions::OPTION_DATABASE => $this->dbconfig['database'],
            // server endpoint to connect to
            ArangoConnectionOptions::OPTION_ENDPOINT => 'tcp://'.$this->dbconfig['host'].':'.$this->dbconfig['port'],
            // authorization type to use (currently supported: 'Basic')
            ArangoConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
            // user for basic authorization
            ArangoConnectionOptions::OPTION_AUTH_USER => $this->dbconfig['username'],
            // password for basic authorization
            ArangoConnectionOptions::OPTION_AUTH_PASSWD => $this->dbconfig['password'],
            // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
            ArangoConnectionOptions::OPTION_CONNECTION => 'Keep-Alive',
            // connect timeout in seconds
            ArangoConnectionOptions::OPTION_TIMEOUT => 3,
            // whether or not to reconnect when a keep-alive connection has timed out on server
            ArangoConnectionOptions::OPTION_RECONNECT => true,
            // optionally create new collections when inserting documents
            ArangoConnectionOptions::OPTION_CREATE => true,
            // optionally create new collections when inserting documents
            ArangoConnectionOptions::OPTION_UPDATE_POLICY => ArangoUpdatePolicy::LAST,
        ];
        $this->connection = new ArangoConnection($connectionOptions);
        return $this->connection;
    }


    //Create a Collection
    public function createCollection($collectionName=NULL){
        if ($collectionName) {
            $collectionHandler = new ArangoCollectionHandler($this->connection);

            // clean up first
            if ($collectionHandler->has($collectionName)) {
                $collectionHandler->drop($collectionName);
            }

            // create a new collection
            $userCollection = new ArangoCollection();
            $userCollection->setName($collectionName);
            $id = $collectionHandler->create($userCollection);

            // check if the collection exists
            $result = $collectionHandler->has($collectionName);
            if ($result){
                //If Successfully Created Return the Collection ID
                return $id;
            }else{
                //If Not successful
                return false;
            }
        }
    }


    public function insert($collection=NULL,$dataArray=NULL){
        $handler = new ArangoDocumentHandler($this->connection);

        // create a new document
        $doc = new ArangoDocument();

        // use set method to set document properties
        foreach ($dataArray as $key => $value){
            $doc->set($key, $value);
        }

        // use magic methods to set document properties
        //$doc->likes = ['fishing', 'hiking', 'swimming'];

        // send the document to the server
        $id = $handler->save($collection, $doc);

        // check if a document exists
        $result = $handler->has($collection, $id);
        if ($result){
            //If Document creation is successful
            return $id;
        }else{
            return false;
        }
    }


}