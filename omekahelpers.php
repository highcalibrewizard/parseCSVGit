    /Helper functions for Importing from Excel -> CSV
	//Copy to plugins/ImportExport/mods/csvimport/import.php
	
    /**
     * Addin in custom relations based on string value in certain fields.
     * Used in importing from client that has a personal relation linked ony by name (previously Ronneby kommun)
     * You will need to customize the code yourself for the fields in question
     * This code is not optimized and you should set the correct columns manually in excel/CSV
     * Usage: Called from _addItemFromRow, before the item is recorded to _recordImportedItemId
     * Dont forget to comment that line after importing!!!
     * @param Item
     */
    private function _checkCustomRelation($item) 
    {
        
        if (!empty(implode('',$item->getElementTexts('Item Type Metadata', 'Låntagare')))) {
            $select = $this->_db->select()->from($this->_db->individuals)->where('efternamn = ?', 
                    implode('',$item->getElementTexts('Item Type Metadata', 'Låntagare')))->limit(1);
            $individual = $this->_db->getTable('individuals')->fetchRow($select);
            if($individual)
                $this->_addCustomRelation($item, 'Låntagare',$individual['id']);
        }
        
        if (!empty(implode('',$item->getElementTexts('Item Type Metadata', 'Konstnär / gravör - efternamn')))) {
            $select = $this->_db->select()->from($this->_db->individuals)->where('efternamn = ?', 
                    implode('',$item->getElementTexts('Item Type Metadata', 'Konstnär / gravör - efternamn')))->limit(1);
            $individual = $this->_db->getTable('individuals')->fetchRow($select);
            if($individual)
                $this->_addCustomRelation($item, 'Konstnär', $individual['id']);
        }
    }
    
    /**
     * Called from _checkCustomRelation. Adds in the relation to the db
     * @param Item $item
     * @param String $relationOld name of the relation. Hard coded in checkCustomRelation
     * @param Person:id that is saved as id_old in Sofie db
     */
    private function _addCustomRelation($item, $relationOld,$personid) {
        $re = new IndividualRelation();
        $re->objektid = $item->id;
        $re->created_by_user_id = current_user()->id;
        $re->modified_by_user_id = current_user()->id;
        $re->personid = $personid;
        
        $select = $this->_db->select()->from($this->_db->relation_types, array('id'))->where('namn = ?', $relationOld)->limit(1);
        $relationtypeids = $this->_db->getTable('relation_types')->fetchRow($select);
        
        if($relationtypeids){ //Ta första bästa
            $re->relationid = $relationtypeids['id'];
        } else{ //Om relationstypen inte kunde hittas, skapa en ny relationstyp
            $relationtype = new RelationType;
            $relationtype->id_old = null;
            $relationtype->created_by_user_id = current_user()->id;
            $relationtype->modified_by_user_id = current_user()->id;
            $relationtype->namn = $relationOld;
            $relationtype->beskrivning = "Automatiskt skapad vid importering med Sofie import.";
            $relationtype->save();
            $re->relationid = $relationtype->id;
        }
        
        $re->save();
    }
    