<?php

/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	SSP Form
*   Routine:	File.php
*   Created:	30-Sep-2016
*   Descrip:	class to upload files to the server, One object per file upload field in form->fileObjects array.
*
*   Revision:	a
*   Rev. Date	30-Sep-2016
*   Descrip:	Created.
*/
namespace w34u\ssp\sfc;

class File{
	/**
	 * file upload form element name
	 * @var string
	 */
	public $elName;
	/**
	 * directory wher the file will be saved
	 * @var string
	 */
	public $targetDir;
	/**
	 * path to view an image
	 * @var string
	 */
	public $targetView;
	/**
	 * name of file to be saved
	 * @var string
	 */
	public $fileName;
	/**
	 * name of old file from db or submit with preview
	 * @var string
	 */
	public $fileNameOld;
	/**
	 * procedure to generate the unique part of the file name
	 * @var string
	 */
	public $fileNameRoutine = "";
	/**
	 * Object to which the filename routine belongs
	 * @var object 
	 */
	private $routineObject = null;
	/**
	 * totally replace the file name with this, keep extension
	 * @var string
	 */
	public $fileNameReplace = "";
	/**
	 * array of valid file types
	 * @var array
	 */
	public $validTypes;
	/**
	 * maximum file size allowed in kbytes
	 * @var int
	 */
	public $maxSize;

	// preview params
	/**
	 * preview upload
	 * @var bool
	 */
	public $preview = false;
	/**
	 * directory used for preview
	 * @var string
	 */
	public $previewDir;
	/**
	 * path to view image in preview directory
	 * @var string
	 */
	public $previewView;
	/**
	 * file is in preview directory
	 * @var bool
	 */
	public $fileInPreview = false;

	/**
	 * valid file upload on this submit
	 * @var bool
	 */
	public $fileUploaded = false;
	/**
	 * on a file upload file information
	 * @var array
	 */
	public $fileInfo;
	/**
	 * extension of file uploaded
	 * @var string
	 */
	public $fileNameExtension;
	/**
	 * rest of file name without extension
	 * @var string
	 */
	public $fileNameRest;
	/**
	 * error during file upload
	 * @var bool
	 */
	public $error = false;
	/**
	 * error result
	 * @var string
	 */
	public $errorText = "";

	/**
	 * Constructor
	 * @param string $el - file upload form element name
	 * @param string $targetDir - where the file is going to go on succesfull upload
	 * @param string $targetView - path to view the uploaded file
	 * @param array $validTypes - valid file types for this upload
	 * @param int $maxSize - maximum number of kBytes allowed
	 * @param string $fileNameRoutine - eval'd to produce an addition to the file name to guarantee unique
	 * @param object $routineObject - Object to which the filename routine belongs
	 * @param string $fileNameReplace - totally replace the file name with this, keeps the extension
	 */
	public function __construct($el, $targetDir, $targetView, $validTypes, $maxSize, $fileNameRoutine="", $routineObject = null, $fileNameReplace=""){
		if(!is_object($el) or get_class($el) != "w34u\ssp\sfc\Fe" or $el->type != "file"){
			trigger_error("File: need a valid form file form element", E_USER_ERROR);
		}

		$this->elName = $el->name;
		$this->desc = $el->description;
		if($targetDir != "" and file_exists($targetDir)){
			$this->targetDir = $targetDir;
		}
		else{
			SSP_error("File::construct: file upload object, invalid target directory: $targetDir for $this->desc");
		}
		$this->targetView = $targetView;
		if(is_array($validTypes)){
			$this->validTypes = $validTypes;
		}
		else{
			SSP_error("File::construct: file upload object, needs valid file types array for $this->desc");
		}
		$this->maxSize = $maxSize;
		if($fileNameRoutine != ""){
			$this->fileNameRoutine = $fileNameRoutine;
		}
		if(is_object($routineObject)){
			$this->routineObject = $routineObject;
		}
		if($fileNameReplace != ""){
			$this->fileNameReplace = $fileNameReplace;
		}
	}

	public function setPreview($previewDir, $previewView){
		// configure preview function
		//
		// parameters
		//	$previewDir - string - path to preview directory
		//	$previewView - string - path to view preview

		$this->preview = true;
		if($previewDir != "" and file_exists($previewDir)){
			$this->previewDir = $previewDir;
		}
		else{
			SSP_error("File::construc: file upload object, invalid preview directory: $previewDir for $this->desc");
		}
		$this->previewView = $previewView;
	}

    public function upload(){
    	// check for upload of file and move to specified directory

         if(isset($_FILES[$this->elName]) and is_uploaded_file($_FILES[$this->elName]['tmp_name'])){
            // a file has been uploaded
            $this->fileInfo = $_FILES[$this->elName];
            if($this->fileInfo["size"] <= $this->maxSize){
				$periodPos = strrpos($this->fileInfo["name"], ".");
				// get extension
				$this->fileNameExtension = strtolower(substr($this->fileInfo["name"], $periodPos));
				// get rest of file name and remove all spaces and lower case it
				$this->fileNameRest = str_replace(""," ",strtolower(substr($this->fileInfo["name"], 0, $periodPos)));
				if(array_search($this->fileNameExtension, $this->validTypes) !== false){
					// valid file type uploaded
					if($this->fileNameRoutine != ""){
						if(is_object($this->routineObject)){
							$this->fileName = $this->fileNameRest. call_user_func([$this->routineObject, $this->fileNameRoutine]) . $this->fileNameExtension;
						}
						else{
							$this->fileName = $this->fileNameRest. call_user_func($this->fileNameRoutine) . $this->fileNameExtension;
						}
					}
					elseif($this->fileNameReplace != ""){
						$this->fileName = $this->fileNameReplace . $this->fileNameExtension;
					}
					else{
						$this->fileName = $this->fileNameRest . $this->fileNameExtension;
					}
					if(!$this->preview){
						$directory = $this->targetDir;
					}
					else{
						$directory = $this->previewDir;
					}
					if(move_uploaded_file($this->fileInfo["tmp_name"], $directory. $this->fileName)){
						$this->fileUploaded = true;
						if($this->fileNameOld and $this->fileNameOld != $this->fileName and file_exists($directory. $this->fileNameOld)){
							// delete old file if it exists
							unlink($directory. $this->fileNameOld);
						}
					}
					else{
						$this->error("Failed to move file to target directory $directory for $this->desc");
					}
				}
				else{
					$this->error("File uploaded is of invalid type ". $this->fileNameExtension. "  for $this->elName");
				}
			}
			else{
				$this->error("File uploaded is too big ". $this->fileInfo["size"]. " bytes for $this->elName");
			}
        }
    	return($this->fileUploaded);
    }

    public function getDisplay($data){
    	// returns a path to display the file using a browser
    	//
    	// parameters
    	//	$data - array - submitted data

    	// file name from form data
    	if(isset($data[$this->elName. "Display"]) and trim($data[$this->elName. "Display"]) != ""){
    		$fileName = $data[$this->elName. "Display"];
    	}
    	elseif(isset($data[$this->elName]) and trim($data[$this->elName]) != ""){
    		$fileName = $data[$this->elName];
    	}
    	else{
    		$fileName = false;
    	}
    	$this->fileNameOld = $fileName;

		if(!$this->preview){
			$directory = $this->targetDir;
			$directoryView = $this->targetView;
		}
		else{
			$directory = $this->previewDir;
			$directoryView = $this->previewView;
		}

    	$file = false;
    	if($this->fileUploaded){
    		// path for just uploaded file
    		$file = $directoryView. $this->fileName;
    	}
    	elseif(!$this->preview and $fileName and $this->isFileName($fileName) and file_exists($directory. $fileName)) {
    		// file name from submitted form data and is valid and exists
    		$file = $directoryView. $fileName;
    		$this->fileName = $fileName;
    	}
    	elseif($this->preview and $fileName){
    		// check if file is in preview or target directories
    		if($this->isFileName($fileName)){
    			if(file_exists($directory. $fileName)){
    				$file = $directoryView. $fileName;
    				$this->fileInPreview = true;
    			}
    			elseif(file_exists($this->targetDir. $fileName)){
    				$file = $this->targetView. $fileName;
    			}
     		$this->fileName = $fileName;
   			}
    	}
    	return($file);
    }

    public function move(){
    	// moves file from preview to target directories
    	// returns path to target directory

    	if($this->fileInPreview){
			copy($this->previewDir. $this->fileName, $this->targetDir. $this->fileName);
			unlink($this->previewDir. $this->fileName);
		}
		return($this->targetDir. $this->fileName);
    }

    public function error($text){
    	// sets an error for file upload

    	$this->error = true;
    	$this->errorText = $text;
    }

	public function isFileName($data){
        // Validates the data as filename - 0 to 9, a - z, A - Z, -_.
        //
        // parameters
        //  $data - string - data to be verified
        //
        //  returns true on valid data else list of valid characters

        // check it has valid characters
        $validChars = "0-9a-zA-z-_.";
        $errorString = "0 to 9, a to z, A to Z and -_.";
        if((strlen($data) > 4) and preg_match("/[". $validChars. "]/", trim($data))){
        	return(true);
        }
        else{
        	return(false);
        }
    }
}

/* End of file File.php */
/* Location: ./src/sfc/File.php */