{foreach $list as $product}
	{foreach $product['customization'] as $customization}
		<tr>
		<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td>
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							<strong>{$product['name']|replace:' - Location : journée':''}</strong><br>
							{$customization['customization_text']|replace:'<br />':''} pour une durée de {$customization['customization_quantity']} jour{if $customization['customization_quantity']>1}s{/if}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
		{*<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td align="right">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{$product['unit_price']}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>*}
		<td style="border:1px solid #D6D4D4;">
			<table class="table" style="width:100%">
				<tr>
					<td width="10">&nbsp;</td>
					<td align="right">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{$customization['quantity']}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	{/foreach}
{/foreach}