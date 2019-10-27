<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WidgetCalculator extends CI_Controller {
	
	/**
	 * Allows us to sort a multi-dimensional array by a specific key
	 * See https://www.php.net/usort
	 */
	public function cmp($a, $b)
	{
		//Our widgets should not be equal, but could become relevant
		if ($a["quantity"] == $b["quantity"]) {
			return 0;
		}
		return ($a["quantity"] > $b["quantity"]) ? -1 : 1;
	}
	
	/**
	 * Index Page, the widget calculator form allowing input of widget requirement, also takes the POST data and outputs result
	 */
	public function index()
	{
		$widgetCount = $this->input->post('widgetCount');
		
		if ($widgetCount != null)
		{
			//We have a widget order size, so we can calculate the pack requirement
			$data ['widgetCount'] = $widgetCount;
			
			//Load the pack sizes from the packs.json file
			$packsJsonFile = file_get_contents("application/config/packs.json");
			//Convert to array 
			$packTypes = json_decode($packsJsonFile, true)["packs"];

			//We are going to loop through the available widget packs so ensure that it is numerically ordered (descending)
			usort($packTypes,  array($this, "cmp"));
			
			$packTypeCount = count($packTypes); //Pack type count remains fixed now
			
			//Now we break apart our widget count pack by pack, keeping track of what packs are used
			$remainingWidgets = $widgetCount;
			
			//Whilst we have widgets to ship
			while ($remainingWidgets > 0)
			{
				$i = 0;
				
				//Go through each pack type subtracting from the widget count as many times as possible
				while ($i < $packTypeCount)
				{
					while ($remainingWidgets >= $packTypes[$i]["quantity"])
					{
						$remainingWidgets = $remainingWidgets - $packTypes[$i]["quantity"];
						$packTypes[$i]["shipped"]++;
					}
					
					$i++;
				}
				
				//If the number of remaining widgets left is less than the size in the minimal pack, add one of those and terminate
				if($remainingWidgets > 0 && $remainingWidgets < $packTypes[$packTypeCount-1]["quantity"])
				{
					$packTypes[$packTypeCount-1]["shipped"]++;
					$remainingWidgets = 0;
				}
			}
			
			//Optimize shipment by checking if any of packs can be combined e.g. 2 x 250 = 500
			$optimized = false;
			
			//We are optimized when we can no longer combine widget packs
			while (!$optimized)
			{
				$j = $packTypeCount - 1;
				$optimized = true;
				
				while ($j >= 0)
				{
					if ($packTypes[$j]["shipped"] > 0)
					{
						//Go through larger pack sizes to see if anything fits
						$k = ($j-1);
						while ($k >= 0)
						{
							//Gradually add up the widgets and if there is a chance to combine into the larger (k) then do so
							$l = 0;
							
							//This is the amount of widgets we are trying to force into a larger pack
							$bubblingValue = 0;
							
							while ($l < $packTypes[$j]["shipped"])
							{
								$bubblingValue = $bubblingValue + $packTypes[$j]["quantity"];
								
								//Our solution is not yet optimized
								if ($bubblingValue >= $packTypes[$k]["quantity"])
								{
									$optimized = false;
									
									$spare = $bubblingValue - $packTypes[$k]["quantity"];
									//Remove l packs from one pack size to one of the larger
									$packTypes[$j]["shipped"] = $packTypes[$j]["shipped"] - ($l+1);
									$packTypes[$k]["shipped"]++;
									
									if ($spare > 0)
									{
										//Deal with spare widgets by adding minimal pack size they will fit in
										for ($p = $packTypeCount-1; $p >= 0; $p--)
										{
											if ($spare <= $packTypes[$p]["quantity"])
											{
												$packTypes[$p]["shipped"] ++;
												$p = -1;
											}
										}
									}
									
									//One bubbling per iteration so escape all loops except main optimisation
									$j=0; $l = $packTypes[$j]["shipped"]; $k=0;
								}
								
								$l ++;
							}
							
							$bubblingValue = 0;
							$k--;
						}
					}
					$j--;
				}
			}
			//Add shipment data into the view data to display
			$data['shipment'] = $packTypes;
			
			$this->load->view('widget_form', $data);
		}
		else
		{
			$this->load->view('widget_form');
		}
	}
}
