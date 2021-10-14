<?php 
/**
 * Mytable Page Controller
 * @category  Controller
 */
class MytableController extends BaseController{
	function __construct(){
		parent::__construct();
		$this->tablename = "mytable";
	}
	/**
     * List page records
     * @param $fieldname (filter record by a field) 
     * @param $fieldvalue (filter field value)
     * @return BaseView
     */
	function index($fieldname = null , $fieldvalue = null){
		$request = $this->request;
		$db = $this->GetModel();
		$tablename = $this->tablename;
		$fields = array("id", 
			"title", 
			"about", 
			"organizer", 
			"timestamp", 
			"email", 
			"address", 
			"latitude", 
			"longitude", 
			"tags0", 
			"tags1", 
			"tags2", 
			"tags3", 
			"tags4", 
			"tags5", 
			"tags6");
		$pagination = $this->get_pagination(21); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				mytable.id LIKE ? OR 
				mytable.title LIKE ? OR 
				mytable.about LIKE ? OR 
				mytable.organizer LIKE ? OR 
				mytable.timestamp LIKE ? OR 
				mytable.email LIKE ? OR 
				mytable.address LIKE ? OR 
				mytable.latitude LIKE ? OR 
				mytable.longitude LIKE ? OR 
				mytable.tags0 LIKE ? OR 
				mytable.tags1 LIKE ? OR 
				mytable.tags2 LIKE ? OR 
				mytable.tags3 LIKE ? OR 
				mytable.tags4 LIKE ? OR 
				mytable.tags5 LIKE ? OR 
				mytable.tags6 LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "mytable/search.php";
		}
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("timestamp", "ASC");
		}
		$db->where("timestamp >= CURRENT_TIMESTAMP()");
		if($fieldname){
			$db->where($fieldname , $fieldvalue); //filter by a single field name
		}
		$tc = $db->withTotalCount();
		$records = $db->get($tablename, $pagination, $fields);
		$records_count = count($records);
		$total_records = intval($tc->totalCount);
		$page_limit = $pagination[1];
		$total_pages = ceil($total_records / $page_limit);
		if(	!empty($records)){
			foreach($records as &$record){
				$record['about'] = str_truncate($record['about'],50,'...');
$record['timestamp'] = relative_date($record['timestamp']);
			}
		}
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = $records_count;
		$data->total_records = $total_records;
		$data->total_page = $total_pages;
		if($db->getLastError()){
			$this->set_page_error();
		}
		$page_title = $this->view->page_title = "Mytable";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("mytable/list.php", $data); //render the full page
	}
	/**
     * View record detail 
	 * @param $rec_id (select record by table primary key) 
     * @param $value value (select record by value of field name(rec_id))
     * @return BaseView
     */
	function view($rec_id = null, $value = null){
		$request = $this->request;
		$db = $this->GetModel();
		$rec_id = $this->rec_id = urldecode($rec_id);
		$tablename = $this->tablename;
		$fields = array("id", 
			"title", 
			"about", 
			"organizer", 
			"timestamp", 
			"email", 
			"address", 
			"latitude", 
			"longitude", 
			"tags0", 
			"tags1", 
			"tags2", 
			"tags3", 
			"tags4", 
			"tags5", 
			"tags6");
		if($value){
			$db->where($rec_id, urldecode($value)); //select record based on field name
		}
		else{
			$db->where("mytable.id", $rec_id);; //select record based on primary key
		}
		$record = $db->getOne($tablename, $fields );
		if($record){
			$record['about'] = str_truncate($record['about'],50,'...');
$record['timestamp'] = relative_date($record['timestamp']);
			$page_title = $this->view->page_title = "View ";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		}
		else{
			if($db->getLastError()){
				$this->set_page_error();
			}
			else{
				$this->set_page_error("No record found");
			}
		}
		return $this->render_view("mytable/view.php", $record);
	}
}
