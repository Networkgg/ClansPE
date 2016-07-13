<?php

interface ClanDataProvider{
	/**
	 * Call the <code>$callback</code> parameter with one parameter: the {@link Clan} of the given name, or
	 * <code>null</code> if there is no such clan.
	 *
	 * @param string   $name
	 * @param callable $callback
	 */
	public function getClan($name, callable $callback);

	/**
	 * Call the <code>$callback</code> parameter with one parameter: the {@link Clan} of the given <code>$id</code>,
	 * or <code>null</code> if there is no such clan.
	 *
	 * @param int      $id
	 * @param callable $callback
	 *
	 * @return
	 */
	public function getClanById($id, callable $callback);

	/**
	 * Call the <code>$callback</code> parameter with one parameter: the {@link Clan} of the given {@link Player},
	 * or <code>null</code> if the player is not in a clan or the player isn't registered yet.
	 *
	 * @param Player   $player
	 * @param callable $callback
	 *
	 * @return
	 */
	public function getClanForPlayer(Player $player, callable $callback);

	/**
	 * Finalizes the database, if necessary.
	 */
	public function close();
}
