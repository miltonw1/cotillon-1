<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function leer( $id ) {
		// Sanitizar entrada de datos
		$id = intval( $id );
		$this->db->where('id_venta', $id);
		$this->db->join('clientes', 'clientes.id_cliente = ventas.id_cliente');
		return $this->db->get('ventas')->row_array();
	}

	public function crear( $id_cliente, $total ) {
		$this->db->trans_start();
		// Sanitizar entrada de datos
		$id_cliente = intval( $id_cliente );
		$total = floatval($total);

		// Arreglo de datos
		$data = [
			'id_cliente' => $id_cliente,
			'total' => $total
		];

		// Ejecutar consulta
		$retorno = $this->db->insert( 'ventas', $data );
		return $retorno ? $this->db->insert_id() : false;
	}

	public function actualizar( $id, $id_cliente, $total ) {
		// Sanitizar entrada de datos
		$id = intval( $id );
		$id_cliente = intval( $id_cliente );
		$total = floatval( $total );

		// Arreglo de datos
		$data = array(
      'id_cliente' => $id_cliente,
			'total' => $total
		);

		// Ejecutar consulta
		$this->db->where( 'id_venta', $id );
		$this->db->update( 'ventas', $data );
		// Completo transacción
		$this->db->trans_complete();
		// retorno de true o false por si actualicé
		return boolval( $this->db->affected_rows() );
	}

	public function eliminar( $id ) {
		// Sanitizar entrada de datos
		$id = intval( $id );

		$this->db->where('id_venta', $id);
		$this->db->delete('ventas');

		return boolval( $this->db->affected_rows() );
	}

	public function lista( $paginacion = 1 ) {

    $desde = ($paginacion - 1) * 100;
    // $hasta = $paginacion * 100; REDUNDANTE

    $this->db->order_by('fecha', 'DESC');
		$this->db->join('clientes', 'clientes.id_cliente = ventas.id_cliente');
    $this->db->limit( 100, $desde ); // $hasta - $desde = 1*100 = 100 siempre
		return $this->db->get('ventas')->result_array();
	}

	public function hasta($fecha = '') {
		if ( $fecha ) {
			$this->db->order_by('fecha', 'DESC');
			$this->db->join('clientes', 'clientes.id_cliente = ventas.id_cliente');
			$this->db->where( 'fecha >=', $fecha->format('Y-m-d H:i:S') ); // $hasta - $desde = 1*100 = 100 siempre
			return $this->db->get('ventas')->result_array();
		} else return $this->lista();
	}

	public function last_id() {
		return intval( $this->db->query('SELECT LAST_INSERT_ID();')->row_array()['LAST_INSERT_ID()'] );
	}

	public function contar_total()
	{
		return $this->db->count_all('ventas');
	}
}
