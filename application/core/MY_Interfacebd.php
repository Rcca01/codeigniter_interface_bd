<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * @author Anusio
 * Classe para manipulação generica de sql
 * sem or_where or_like.
 */
class MY_Interfacebd extends CI_Model {
	private $table;
	private $where;
	private $join;
	private $like;
	private $insert_id;
	private $order_by_t = null;
	private $order_by_a = '';
	/**
	 * construtor
	 * @param $table = o nome da tabela principal
	 */
	function __construct() {
		parent::__construct();
		$this->inicialize();
		$this->load->database();
	}
	
	/**
	 * Exibe a query...
	 *
	 * Para desenvolvedor apenas...
	 */
	public function exibe_query() {
		$this->output->enable_profiler(TRUE);
	}

	/**
	 * reseta as variaveis privadas, usar isto antes de tudo
	 * @param a tabela principal
	 */
	public function inicialize($table = '') {
		$this->table = $table;
		$this->where = array();
		$this->join = array();
		$this->like = array();
		$this->insert_id = -1;
		$this->order_by_t = null;
		$this->order_by_a = '';
	}


	/**
	 * Retorna a tabela que está referencia no objeto chamado
	 * @return Retorna o nome da tabela
	 */
	public function get_table()
	{
		return $this->table;
	}

	/**
	 * set where
	 * @param $where a array where
	 * @throws exeption if not a array
	 */
	public function set_where($where = '') {
		if (!is_array($where)) {
			throw new RuntimeException('where não é array', 1);
		}
		$this->where = $where;
	}

	/**
	 * set join
	 * @param $join a array join
	 * @throws exeption if not a array
	 */
	public function set_join($join = '') {
		if (!is_array($join)) {
			throw new RuntimeException('join não é array', 1);
		}
		$this->join = $join;
	}

	/**
	 * set like
	 * @param $like a array like
	 * @throws exeption if not a array
	 */
	public function set_like($like = '') {
		if (!is_array($like)) {
			throw new RuntimeException('like não é array', 1);
		}
		$this->like = $like;
	}

	/**
	 * get last insert_id ou -1
	 */
	public function get_insert_id() {
		return $this->insert_id;
	}

	/**
	 * orderby
	 * @param $collun a coluna
	 * @param $asc asc desc
	 */
	public function order_by($collun = '', $asc) {
		$this->order_by_t = $collun;
		$this->order_by_a = $asc;
	}

	/**
	 * select da tabela passada no construtor
	 * @param $select o sql select
	 * @param $limit quantidade de resultados
	 * @param $offset sequencia da quantidade de resultados TODO verificar se nao é o contrario
	 * @param $join mudar de left para nada e right todos (quase sempre uso left...)
	 * @return Retorna uma matriz com dados que vão de 0 a n tuplas
	 */
	public function select($select = '*', $limit = 0, $offset = 0 , $join = "left") {
		$this ->db->select($select);
		$this->db->where($this->where);
		$this->db->like($this->like);
		foreach ($this->join as $key => $value) {
			$this->db->join($key, $value, $join);
		}
		if ($this->order_by_t != null) {
			$this->db->order_by($this->order_by_t, $this->order_by_a);
		}
		$resp = $this->db->get($this->table, $limit, $offset);
		return $resp->result_array();
	}
	
	/**
	 * select da tabela passada no construtor
	 * @param $select o sql select
	 * @param $limit quantidade de resultados
	 * @param $offset sequencia da quantidade de resultados TODO verificar se nao é o contrario
	 * @param $group_by group_by
	 * @param $join mudar de left para nada e right todos (quase sempre uso left...)
	 * @return Retorna uma matriz com dados que vão de 0 a n tuplas
	 */
	public function select_group_by($select = '*', $limit = 0, $offset = 0 , $group_by = null, $join = "left") {
		$this->db->select($select);
		$this->db->where($this->where);
		$this->db->like($this->like);
		foreach ($this->join as $key => $value) {
			$this->db->join($key, $value, $join);
		}
		if ($this->order_by_t != null) {
			$this->db->order_by($this->order_by_t, $this->order_by_a);
		}
		$this->db->group_by($group_by);
		
		$resp = $this->db->get($this->table, $limit, $offset);
		return $resp->result_array();
	}
	
	/**
	 * select da tabela passada no construtor o ultimo, select normal orden decendente
	 * @param $orderby...
	 * @return Retorna uma matriz com dados que vão de 0 a n tuplas
	 */
	public function last($orderby = '*', $join = "left") {
		$this->db->where($this->where);
		$this->db->like($this->like);
		foreach ($this->join as $key => $value) {
			$this->db->join($key, $value, $join);
		}
		$this->db->order_by($orderby, 'desc');
		$resp = $this->db->get($this->table, 1, 0);
		return $resp->result_array();
	}
	
	/**
	 * select_max da tabela passada no construtor
	 * @param $select o sql select
	 */
	public function select_max($select = '*') {
		$this->db->select_max($select);
		$this->db->where($this->where);
		$this->db->like($this->like);
		foreach ($this->join as $key => $value) {
			$this->db->join($key, $value);
		}
		$resp = $this->db->get($this->table);
		return $resp->result_array();
	}

	/**
	 * @date 29/09/16
	 * Verifica pelos weres likes e joins se os elementos exitem
	 */
	public function exist() {
		$this->db->where($this->where);
		$this->db->like($this->like);
		foreach ($this->join as $key => $value) {
			$this->db->join($key, $value);
		}
		$resp = $this->db->get($this->table);
		return ($resp->num_rows() > 0);
	}

	/**
	 * select para busca do lasy da tabela passada no construtor
	 * @param $select o sql select
	 * @param $busca a string que será buscada
	 * @param $busca_in array com as colunas que serão buscadas
	 * @param $seq sequencia da quantidade de resultados
	 * @param $qte a quantidade de elementos por busca
	 */
	public function select_busca($select = '*', $busca = '', $busca_in, $seq = 0, $qte = 0) {
		if (!is_numeric($seq)) {
			$seq = 0;
		}
		if (!is_numeric($qte)) {
			$qte = 0;
		}
		$qte = $qte;
		$seq = $qte * $seq;
		if (!is_array($busca_in)) {
			$busca_in = array();
		}
		$temp = explode(" ", $busca);

		//can use codeigniter form validation instead
		if ($busca or $busca === "") {
			$this->db->select($select);
			if (count($busca_in) == 0) {
				$this->db->where($this->where);
			}
			foreach ($busca_in as $k => $v) {
				$flagchange = true;
				foreach ($temp as $key => $value) {
					if ($flagchange) {
						$this->db->or_like($v, $value);
						$flagchange = false;
					}
					$this->db->like($v, $value);
				}
				$this->db->where($this->where);
			}

			$this->db->order_by($this->order_by_t, $this->order_by_a);

			$resp = $this->db->get($this->table, $qte, $seq);
			$value = $resp->result_array();
			//Count
			$this->db->select('count(*) as qte');
			if (count($busca_in) == 0) {
				$this->db->where($this->where);
			}
			foreach ($busca_in as $k => $v) {
				$flagchange = true;
				foreach ($temp as $key => $value) {
					if ($flagchange) {
						$this->db->or_like($v, $value);
						$flagchange = false;
					}
					$this->db->like($v, $value);
				}
				$this->db->where($this->where);
			}

			$resp = $this->db->get($this->table, $qte, $seq);
			$resp = $resp->result_array();
			$value['qte'] = $resp[0]['qte'];
		} else {
			$value = array();
			$value['qte'] = 0;
		}
		return $value;
	}

	/**
	 * insert
	 * @param $insert o array inserrt
	 */
	public function insert($insert = '') {
		$this->db->insert($this->table, $insert);
		$this->insert_id = $this->db->insert_id();
	}

	/**
	 * delete
	 */
	public function delete() {
		$this->db->where($this->where);
		$this->db->like($this->like);
		$this->db->delete($this->table);
	}

	/**
	 * update where like
	 * @param update a array update
	 */
	public function update($update = '') {
		$this->db->where($this->where);
		$this->db->like($this->like);
		return $this->db->update($this->table, $update);
	}

	/**
	 * insere se where e like na table nao existe, update caso contrario
	 * @param o array dos dados que serõ inseridos
	 */
	public function update_insert($insert = '') {
		$this->db->where($this->where);
		$this->db->like($this->like);
		$resp = $this->db->count_all_results($this->table);

		if ($resp > 0) {
			$this->db->where($this->where);
			$this->db->like($this->like);
			$this->db->update($this->table, $insert);
		} else {
			$this->db->insert($this->table, $insert);
			$this->insert_id = $this->db->insert_id();
		}
	}

}