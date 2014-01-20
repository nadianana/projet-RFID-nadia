use strict;

sub scan {
    my ($fn) = @_;

    open(IN, $fn) or return 0;
    open (OUT,">>ENxml") or return 0;
    while(<IN>) {
s&^[^<\t]&\[<AU>&g ;
print OUT $_
    }

    close IN;
    close OUT;
    return 1;}
while(my $fn = shift @ARGV) {
    print "$fn:\n";
    if(!scan($fn)) { print "[Open $fn failed: $!]\n"; }
}
