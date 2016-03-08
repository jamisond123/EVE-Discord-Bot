<?php
/**
 * @param $seat
 * @param $post
 * @param $token
 * @return mixed|null
 */
function seatPost($seat, $post, $token)
{
    try
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'X-Token:' . $token;
        curl_setopt($ch, CURLOPT_URL, $seat);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        return $result;
    }
    catch(Exception $e)
    {
        $this->logger->info("SeAT Error: " . $e->getMessage());
        return null;
    }
}
